<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\Application;
use App\Models\HealthcareWorker;
use App\Models\JobPosting;
use App\Models\NdisRequirementCompleted;
use App\Models\NdisRequirementParameter;
use App\Models\User;

class HealthcareController extends Controller
{
    public function profilePhoto(string $filename)
    {
        abort_unless(preg_match('/\A[A-Za-z0-9._-]+\z/', $filename) === 1, 404);

        $path = storage_path('app/public/profile-photos/' . $filename);

        abort_unless(is_file($path), 404);

        return response()->file($path);
    }

    public function showRegistrationForm()
    {
        return view('healthcare-register');
    }

    public function register(Request $request)
    {
        $healthcareSkillOptions = config('healthcare_skills.options', []);

        $data = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'profession' => 'required|string|max:100',
            'specialization' => 'nullable|string|max:100',
            'licenseNumber' => 'nullable|string|max:100',
            'experience' => 'nullable|integer|min:0',
            'facility' => 'nullable|string|max:255',
            'facilityAddress' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'credentials' => 'nullable|string',
            'skills' => 'nullable|array',
            'skills.*' => ['nullable', 'string', 'max:100', Rule::in($healthcareSkillOptions)],
            'employment_history' => 'nullable|array',
            'employment_history.*.company_name' => 'nullable|string|max:255',
            'employment_history.*.job_position' => 'nullable|string|max:255',
            'employment_history.*.summary' => 'nullable|string',
            'employment_history.*.year_started' => 'nullable|digits:4|integer',
            'employment_history.*.year_ended' => 'nullable|digits:4|integer',
            'employment_history.*.is_currently_employed' => 'nullable|boolean',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // create user account first
        $user = User::create([
            'fullname' => $data['firstName'] . ' ' . $data['lastName'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'accounttype' => 'healthcare_worker',
            'verified' => false,
            'approved' => false,
        ]);

        // create worker profile
        $worker = HealthcareWorker::create([
            'user_id' => $user->id,
            'profession' => $data['profession'],
            'specialization' => $data['specialization'] ?? null,
            'license_number' => $data['licenseNumber'] ?? null,
            'experience_years' => $data['experience'] ?? null,
            'facility_name' => $data['facility'] ?? null,
            'facility_address' => $data['facilityAddress'] ?? null,
            'location' => $data['location'] ?? null,
            'credentials' => $data['credentials'] ?? null,
        ]);

        if (!empty($data['skills'])) {
            foreach ((array) $data['skills'] as $skillText) {
                $skillText = trim($skillText);
                if ($skillText !== '') {
                    $worker->skills()->create(['skill' => $skillText]);
                }
            }
        }

        if (!empty($data['employment_history']) && is_array($data['employment_history'])) {
            foreach ($data['employment_history'] as $history) {
                $companyName = trim($history['company_name'] ?? '');
                $jobPosition = trim($history['job_position'] ?? '');
                $yearStarted = trim($history['year_started'] ?? '');
                $yearEnded = trim($history['year_ended'] ?? '');
                $summary = trim($history['summary'] ?? '');
                $isCurrent = isset($history['is_currently_employed']) && ($history['is_currently_employed'] == '1' || $history['is_currently_employed'] === true);

                if ($companyName && $jobPosition && $yearStarted) {
                    $worker->employmentHistory()->create([
                        'company_name' => $companyName,
                        'job_position' => $jobPosition,
                        'summary' => $summary,
                        'year_started' => $yearStarted,
                        'year_ended' => $yearEnded ?: null,
                        'is_currently_employed' => $isCurrent,
                    ]);
                }
            }
        }

        // send verification email
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id]
        );

        Mail::send('emails.verify', ['url' => $verificationUrl], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify your Our Care Pty Ltd email address');
        });

        return redirect('/login')->with('status', 'Registration successful! Please check your email to verify your account.');
    }

    public function profile()
    {
        $session = Session::get('user');
        $user = User::find($session['id']);
        $worker = HealthcareWorker::with(['skills', 'employmentHistory', 'ndisRequirementsCompleted'])->where('user_id', $user->id)->first();
        $ndisRequirements = NdisRequirementParameter::orderBy('requirements')->get();
        $completedRequirements = $worker
            ? $worker->ndisRequirementsCompleted->keyBy('parameter_id')
            : collect();
        $profileSections = [
            'basic_information' => filled($user?->fullname) && filled($user?->email) && filled($user?->phone),
            'overview' => $worker
                && filled($worker->facility_name)
                && filled($worker->location)
                && filled($worker->experience_years)
                && filled($worker->license_number),
            'professional_summary' => $worker && filled($worker->credentials),
            'work_details' => $worker
                && filled($worker->profession)
                && filled($worker->specialization)
                && filled($worker->license_number)
                && filled($worker->experience_years),
            'skills' => $worker && $worker->skills->pluck('skill')->filter()->isNotEmpty(),
            'employment_summary' => $worker && $worker->employmentHistory->isNotEmpty(),
        ];
        $incompleteProfileSections = collect($profileSections)->filter(fn ($complete) => ! $complete)->count();
        $completedNdisCount = $completedRequirements->filter(fn ($completed) => filled($completed->document_link))->count();
        $missingNdisCount = max($ndisRequirements->count() - $completedNdisCount, 0);
        $profileCompletionPercentage = min(100, max(0, 20 - ($incompleteProfileSections * 2) + ($completedNdisCount * 5)));

        return view('healthcare-profile', compact(
            'user',
            'worker',
            'ndisRequirements',
            'completedRequirements',
            'profileCompletionPercentage',
            'incompleteProfileSections',
            'missingNdisCount'
        ));
    }

    /**
     * Show job postings page.
     * If the current user is not approved by admin, return view with flag so
     * that the blade template can display an informational message instead of
     * the jobs list.
     */
    public function jobs()
    {
        $session = Session::get('user');
        $user = User::find($session['id']);
        $approved = $user ? ($user->approved == 1) : false;

        $jobPostings = collect();
        $jobPostsData = [];

        if ($approved) {
            $jobPostings = JobPosting::with(['client', 'requirements', 'keySkills'])
                ->orderByDesc('created_at')
                ->get();

            $sessionUser = Session::get('user');
            $appliedJobIds = [];
            if ($sessionUser && isset($sessionUser['id'])) {
                $appliedJobIds = Application::where('user_applied_id', $sessionUser['id'])
                    ->pluck('job_posting_id')
                    ->toArray();
            }

            $jobPostsData = $jobPostings->map(function ($job) use ($appliedJobIds) {
                $clientName = $job->client && $job->client->alias ? $job->client->alias : ($job->client ? trim($job->client->first_name . ' ' . $job->client->last_name) : null);

                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'facility' => $clientName ?: ($job->client?->email ?? 'Unknown'),
                    'location' => $job->location,
                    'salary' => ($job->minimum_pay_offer && $job->maximum_pay_offer)
                        ? '$' . number_format($job->minimum_pay_offer, 0) . ' - $' . number_format($job->maximum_pay_offer, 0)
                        : null,
                    'type' => $job->employment_type,
                    'experience' => $job->experience,
                    'specialty' => $job->specialty,
                    'description' => $job->description,
                    'key_skills' => $job->keySkills->pluck('skill'),
                    'requirements' => $job->requirements->pluck('description'),
                    'posted' => $job->created_at->diffForHumans(),
                    'applied' => in_array($job->id, $appliedJobIds),
                ];
            })->toArray();
        }

        return view('healthcare-jobs', compact('approved', 'jobPostsData'));
    }

    public function jobDetails($id)
    {
        $session = Session::get('user');
        $user = User::find($session['id']);
        $approved = $user ? ($user->approved == 1) : false;

        if (!$approved) {
            return redirect('/healthcare-jobs');
        }

        $job = JobPosting::with(['client', 'requirements', 'keySkills'])->find($id);
        if (!$job) {
            abort(404);
        }

        $clientName = $job->client ? ($job->client->alias ?: trim($job->client->first_name . ' ' . $job->client->last_name)) : 'Unknown';
        $clientId = $job->client?->id;

        $sessionUser = Session::get('user');
        $appliedJobIds = [];
        if ($sessionUser && isset($sessionUser['id'])) {
            $appliedJobIds = Application::where('user_applied_id', $sessionUser['id'])
                ->pluck('job_posting_id')
                ->toArray();
        }

        $jobData = [
            'id' => $job->id,
            'title' => $job->title,
            'client_name' => $clientName,
            'client_id' => $clientId,
            'location' => $job->location,
            'salary_min' => $job->minimum_pay_offer ? '$' . number_format($job->minimum_pay_offer, 0) : null,
            'salary_max' => $job->maximum_pay_offer ? '$' . number_format($job->maximum_pay_offer, 0) : null,
            'salary_range' => ($job->minimum_pay_offer && $job->maximum_pay_offer)
                ? '$' . number_format($job->minimum_pay_offer, 0) . ' - $' . number_format($job->maximum_pay_offer, 0)
                : null,
            'employment_type' => $job->employment_type,
            'experience' => $job->experience,
            'specialty' => $job->specialty,
            'description' => $job->description,
            'key_skills' => $job->keySkills->pluck('skill')->toArray(),
            'requirements' => $job->requirements->pluck('description')->toArray(),
            'posted' => $job->created_at->diffForHumans(),
            'posted_date' => $job->created_at->format('M d, Y'),
            'applied' => in_array($job->id, $appliedJobIds),
        ];

        return view('healthcare-jobs-details', compact('jobData', 'approved'));
    }

    public function apply(Request $request, $id)
    {
        $session = Session::get('user');
        if (!$session || !isset($session['id'])) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $userId = $session['id'];
        $alreadyApplied = Application::where('job_posting_id', $id)
            ->where('user_applied_id', $userId)
            ->exists();

        if ($alreadyApplied) {
            return response()->json(['success' => true, 'alreadyApplied' => true]);
        }

        $validated = $request->validate([
            'application_details' => 'nullable|string|max:2000',
            'expected_salary' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|max:5120|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
            $attachmentPath = $request->file('attachment')->store('applications', 'public');
        }

        // Calculate metric score
        $metricScore = $this->calculateMetricScore($userId, $id);

        Application::create([
            'job_posting_id' => $id,
            'user_applied_id' => $userId,
            'application_details' => $validated['application_details'] ?? null,
            'expected_salary' => $validated['expected_salary'] ?? null,
            'attachments' => $attachmentPath,
            'metric_score' => $metricScore,
        ]);

        return response()->json(['success' => true]);
    }

    private function calculateMetricScore($userId, $jobPostingId)
    {
        $score = 0;

        // Get worker data
        $worker = HealthcareWorker::where('user_id', $userId)->first();
        if (!$worker) {
            return $score;
        }

        // Get job posting data
        $jobPosting = JobPosting::find($jobPostingId);
        if (!$jobPosting) {
            return $score;
        }

        // 1. Experience score
        $experienceYears = $worker->experience_years ?? 0;
        if ($experienceYears >= 1 && $experienceYears <= 5) {
            $score += 1;
        } elseif ($experienceYears >= 6 && $experienceYears <= 10) {
            $score += 2;
        } elseif ($experienceYears >= 11 && $experienceYears <= 15) {
            $score += 3;
        } elseif ($experienceYears >= 16 && $experienceYears <= 20) {
            $score += 4;
        } elseif ($experienceYears > 20) {
            $score += 5;
        }

        // 2. Skills matching score
        $workerSkills = $worker->skills->pluck('skill')->map(function($skill) {
            return strtolower(trim($skill));
        })->toArray();

        $jobSkills = $jobPosting->keySkills->pluck('skill')->map(function($skill) {
            return strtolower(trim($skill));
        })->toArray();

        foreach ($workerSkills as $workerSkill) {
            if (in_array($workerSkill, $jobSkills)) {
                $score += 1;
            }
        }

        // 3. Location matching score
        $workerLocation = strtolower(trim($worker->location ?? ''));
        $jobLocation = strtolower(trim($jobPosting->location ?? ''));

        if (!empty($workerLocation) && !empty($jobLocation)) {
            // Check for exact match
            if ($workerLocation === $jobLocation) {
                $score += 2;
            } else {
                // Check for partial matches (city, suburb, state)
                $workerParts = array_map('trim', explode(',', $workerLocation));
                $jobParts = array_map('trim', explode(',', $jobLocation));

                foreach ($workerParts as $workerPart) {
                    foreach ($jobParts as $jobPart) {
                        if (!empty($workerPart) && !empty($jobPart) &&
                            strtolower($workerPart) === strtolower($jobPart)) {
                            $score += 2;
                            break 2; // Exit both loops once we find a match
                        }
                    }
                }
            }
        }

        return $score;
    }

    public function applications()
    {
        $sessionUser = session('user', []);
        $user = User::find($sessionUser['id'] ?? null);

        if (!$user || $user->accounttype !== 'healthcare_worker') {
            return redirect('/login');
        }

        $applications = Application::where('user_applied_id', $user->id)
            ->with(['jobPosting.client'])
            ->latest('created_at')
            ->get();

        return view('applications', compact('applications'));
    }

    public function downloadAttachment($applicationId)
    {
        $application = $this->findOwnedApplication($applicationId);

        if (!$application->attachments || !Storage::disk('public')->exists($application->attachments)) {
            abort(404, 'Attachment not found.');
        }

        $filePath = Storage::disk('public')->path($application->attachments);
        $extension = strtolower(pathinfo($application->attachments, PATHINFO_EXTENSION));

        if ($extension === 'pdf' && request()->boolean('inline')) {
            return response()->file($filePath, ['Content-Type' => 'application/pdf']);
        }

        return Storage::disk('public')->download($application->attachments);
    }

    public function updateApplication(Request $request, $applicationId)
    {
        $application = $this->findOwnedApplication($applicationId);

        $validated = $request->validate([
            'application_details' => 'nullable|string|max:2000',
            'expected_salary' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|max:5120|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
            'remove_attachment' => 'nullable|boolean',
        ]);

        $attachmentPath = $application->attachments;
        $removeAttachment = $request->boolean('remove_attachment');

        if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
            if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {
                Storage::disk('public')->delete($attachmentPath);
            }

            $attachmentPath = $request->file('attachment')->store('applications', 'public');
        } elseif ($removeAttachment && $attachmentPath) {
            if (Storage::disk('public')->exists($attachmentPath)) {
                Storage::disk('public')->delete($attachmentPath);
            }

            $attachmentPath = null;
        }

        $application->update([
            'application_details' => $validated['application_details'] ?? null,
            'expected_salary' => $validated['expected_salary'] ?? null,
            'attachments' => $attachmentPath,
        ]);

        return redirect('/applications')->with('success', 'Application updated successfully.');
    }

    public function destroyApplication($applicationId)
    {
        $application = $this->findOwnedApplication($applicationId);

        if ($application->attachments && Storage::disk('public')->exists($application->attachments)) {
            Storage::disk('public')->delete($application->attachments);
        }

        $application->delete();

        return redirect('/applications')->with('success', 'Application deleted successfully.');
    }

    public function update(Request $request)
    {
        $session = Session::get('user');
        $user = User::find($session['id']);
        $worker = HealthcareWorker::where('user_id', $user->id)->first();
        $healthcareSkillOptions = config('healthcare_skills.options', []);

        $data = $request->validate([
            'profession' => 'required|string|max:100',
            'specialization' => 'nullable|string|max:100',
            'license_number' => 'nullable|string|max:100',
            'experience_years' => 'nullable|integer|min:0',
            'facility_name' => 'nullable|string|max:255',
            'facility_address' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'credentials' => 'nullable|string',
            'skills' => 'nullable|array',
            'skills.*' => ['nullable', 'string', 'max:100', Rule::in($healthcareSkillOptions)],
            'employment_history' => 'nullable|array',
            'employment_history.*.company_name' => 'nullable|string|max:255',
            'employment_history.*.job_position' => 'nullable|string|max:255',
            'employment_history.*.summary' => 'nullable|string',
            'employment_history.*.year_started' => 'nullable|digits:4|integer',
            'employment_history.*.year_ended' => 'nullable|digits:4|integer',
            'employment_history.*.is_currently_employed' => 'nullable|boolean',
            'ndis_requirements_completed' => 'nullable|array',
            'ndis_requirements_completed.*.document_link' => 'nullable|url|max:2048',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_profile_photo' => 'nullable|boolean',
        ]);
        $profileData = $data;
        unset($profileData['ndis_requirements_completed']);
        unset($profileData['profile_photo']);
        unset($profileData['remove_profile_photo']);

        if ($worker) {
            $worker->update($profileData);
        } else {
            $profileData['user_id'] = $user->id;
            $worker = HealthcareWorker::create($profileData);
        }

        if ($request->boolean('remove_profile_photo') && $worker->profile_photo) {
            if (Storage::disk('public')->exists($worker->profile_photo)) {
                Storage::disk('public')->delete($worker->profile_photo);
            }

            $worker->update(['profile_photo' => null]);
        }

        if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
            if ($worker->profile_photo && Storage::disk('public')->exists($worker->profile_photo)) {
                Storage::disk('public')->delete($worker->profile_photo);
            }

            $worker->update([
                'profile_photo' => $request->file('profile_photo')->store('profile-photos', 'public'),
            ]);
        }

        // Sync skills
        if (isset($data['skills'])) {
            $worker->skills()->delete();
            foreach ((array) $data['skills'] as $skillValue) {
                if (trim($skillValue) !== '') {
                    $worker->skills()->create(['skill' => trim($skillValue)]);
                }
            }
        }

        // Sync employment history
        if (isset($data['employment_history'])) {
            $worker->employmentHistory()->delete();
            foreach ((array) $data['employment_history'] as $history) {
                $companyName = trim($history['company_name'] ?? '');
                $jobPosition = trim($history['job_position'] ?? '');
                $yearStarted = trim($history['year_started'] ?? '');
                $yearEnded = trim($history['year_ended'] ?? '');
                $summary = trim($history['summary'] ?? '');
                $isCurrent = isset($history['is_currently_employed']) && ($history['is_currently_employed'] == '1' || $history['is_currently_employed'] === true);

                if ($companyName && $jobPosition && $yearStarted) {
                    $worker->employmentHistory()->create([
                        'company_name' => $companyName,
                        'job_position' => $jobPosition,
                        'summary' => $summary,
                        'year_started' => $yearStarted,
                        'year_ended' => $yearEnded ?: null,
                        'is_currently_employed' => $isCurrent,
                    ]);
                }
            }
        }

        if ($request->has('ndis_requirements_completed')) {
            $submittedRequirements = $request->input('ndis_requirements_completed', []);
            $validParameterIds = NdisRequirementParameter::whereIn('id', array_keys($submittedRequirements))
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();

            foreach ($submittedRequirements as $parameterId => $requirementData) {
                $parameterId = (int) $parameterId;

                if (!in_array($parameterId, $validParameterIds, true)) {
                    continue;
                }

                $documentLink = trim($requirementData['document_link'] ?? '');

                if ($documentLink === '') {
                    NdisRequirementCompleted::where('worker_id', $worker->id)
                        ->where('parameter_id', $parameterId)
                        ->delete();
                    continue;
                }

                NdisRequirementCompleted::updateOrCreate(
                    [
                        'worker_id' => $worker->id,
                        'parameter_id' => $parameterId,
                    ],
                    [
                        'document_link' => $documentLink,
                    ]
                );
            }
        }

        return back()->with('status', 'Profile updated successfully.');
    }

    public function updateProfilePhoto(Request $request)
    {
        $session = Session::get('user');
        $user = User::find($session['id'] ?? null);

        if (!$user) {
            return redirect('/login');
        }

        $data = $request->validate([
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_profile_photo' => 'nullable|boolean',
        ]);

        $worker = HealthcareWorker::firstOrCreate(
            ['user_id' => $user->id],
            ['profession' => 'Healthcare Professional']
        );

        if ($request->boolean('remove_profile_photo') && $worker->profile_photo) {
            if (Storage::disk('public')->exists($worker->profile_photo)) {
                Storage::disk('public')->delete($worker->profile_photo);
            }

            $worker->update(['profile_photo' => null]);
        }

        if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
            if ($worker->profile_photo && Storage::disk('public')->exists($worker->profile_photo)) {
                Storage::disk('public')->delete($worker->profile_photo);
            }

            $worker->update([
                'profile_photo' => $request->file('profile_photo')->store('profile-photos', 'public'),
            ]);
        }

        return back()->with('status', 'Profile picture updated successfully.');
    }

    public function updateSkills(Request $request)
    {
        $session = Session::get('user');
        $user = User::find($session['id'] ?? null);

        if (!$user) {
            return redirect('/login');
        }

        $healthcareSkillOptions = config('healthcare_skills.options', []);

        $data = $request->validate([
            'skills' => 'nullable|array',
            'skills.*' => ['nullable', 'string', 'max:100', Rule::in($healthcareSkillOptions)],
        ]);

        $worker = HealthcareWorker::firstOrCreate(['user_id' => $user->id]);
        $worker->skills()->delete();

        foreach ((array) ($data['skills'] ?? []) as $skillValue) {
            $skillValue = trim($skillValue);

            if ($skillValue !== '') {
                $worker->skills()->create(['skill' => $skillValue]);
            }
        }

        return back()->with('status', 'Skills updated successfully.');
    }

    protected function findOwnedApplication($applicationId): Application
    {
        $sessionUser = session('user', []);
        $user = User::find($sessionUser['id'] ?? null);

        if (!$user || $user->accounttype !== 'healthcare_worker') {
            abort(403, 'Unauthorized user.');
        }

        return Application::where('id', $applicationId)
            ->where('user_applied_id', $user->id)
            ->with(['jobPosting.client'])
            ->firstOrFail();
    }
}
