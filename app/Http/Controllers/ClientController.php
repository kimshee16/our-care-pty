<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Client;
use App\Models\User;
use App\Models\JobPosting;
use App\Models\Application;
use App\Models\Endorsement;
use App\Models\HealthcareWorker;
use App\Models\NdisRequirementParameter;

class ClientController extends Controller
{
    protected function getUserModel()
    {
        $sessionUser = session('user', []);
        return User::find($sessionUser['id'] ?? null);
    }

    protected function isAdmin(): bool
    {
        $user = $this->getUserModel();
        return $user && $user->accounttype === 'admin';
    }

    protected function getRoutePrefix(): string
    {
        return $this->isAdmin() ? 'admin' : 'client';
    }

    /**
     * Show all completed interviews for finalization
     */
    public function finalization()
    {
        $user = $this->getUserModel();
        if (!$user || !in_array($user->accounttype, ['client', 'admin'])) {
            return redirect('/login');
        }

        if ($user->accounttype === 'client') {
            $clientId = $user->record_id;
            $jobPostingIds = JobPosting::where('client_id', $clientId)->pluck('id');
        } else {
            $jobPostingIds = JobPosting::pluck('id');
        }

        $interviews = Application::whereIn('job_posting_id', $jobPostingIds)
            ->whereIn('interview_status', ['completed', 'hired', 'rejected'])
            ->with(['jobPosting', 'applicant'])
            ->orderByDesc('interview_date')
            ->get();
        return view('client-finalization', compact('interviews'));
    }

    public function showRegistrationForm()
    {
        return view('client-register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zipcode' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // create client profile
        $client = Client::create([
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'alias' => $data['alias'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'],
            'date_of_birth' => $data['dob'],
            'address' => $data['address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'zip_code' => $data['zipcode'],
            'country' => $data['country'],
        ]);

        // create user account
        $user = User::create([
            'fullname' => $data['firstName'] . ' ' . $data['lastName'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'accounttype' => 'client',
            'record_id' => $client->id,
            'verified' => false,
            'approved' => false,
        ]);

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

    /**
     * Display client dashboard with dynamic metrics
     */
    public function dashboard()
    {
        $sessionUser = session('user', []);
        $user = User::find($sessionUser['id'] ?? null);
        
        if (!$user || $user->accounttype !== 'client') {
            return redirect('/login');
        }

        $clientId = $user->record_id;

        // Get all job postings by the client
        $activeJobs = JobPosting::where('client_id', $clientId)->count();

        // Get all applications for the client's job postings
        $jobPostingIds = JobPosting::where('client_id', $clientId)->pluck('id');
        $applications = Application::whereIn('job_posting_id', $jobPostingIds)->count();

        // Get scheduled interviews / met and greeted
        $interviews = Application::whereIn('job_posting_id', $jobPostingIds)
            ->where('interview_status', 'scheduled')
            ->count();

        // Get endorsed workers
        $endorsedWorkers = Endorsement::whereIn('job_post_id', $jobPostingIds)->count();

        return view('client-dashboard', compact('activeJobs', 'applications', 'interviews', 'endorsedWorkers'));
    }

    /**
     * Display all applications for the client
     */
    public function applications()
    {
        $user = $this->getUserModel();
        if (!$user || !in_array($user->accounttype, ['client', 'admin'])) {
            return redirect('/login');
        }

        if ($user->accounttype === 'client') {
            $jobPostings = JobPosting::where('client_id', $user->record_id)->get();
        } else {
            $jobPostings = JobPosting::all();
        }

        $jobPostingIds = $jobPostings->pluck('id');

        $applications = Application::whereIn('job_posting_id', $jobPostingIds)
            ->with([
                'jobPosting.keySkills',
                'applicant.healthcareWorker.skills',
                'applicant.healthcareWorker.employmentHistory',
                'applicant.healthcareWorker.ndisRequirementsCompleted.parameter'
            ])
            ->latest('created_at')
            ->get();

        $applications->each(function ($application) {
            $application->match_percentage = $this->calculateMatchPercentage($application);
        });

        return view('client-applications', compact('applications', 'jobPostings'));
    }

    protected function calculateMatchPercentage(Application $application): int
    {
        $jobSkills = collect($application->jobPosting?->keySkills ?? [])
            ->pluck('skill')
            ->map(fn($skill) => mb_strtolower(trim((string) $skill)))
            ->filter()
            ->unique()
            ->values();

        if ($jobSkills->isEmpty()) {
            return 0;
        }

        $workerSkills = collect($application->applicant?->healthcareWorker?->skills ?? [])
            ->pluck('skill')
            ->map(fn($skill) => mb_strtolower(trim((string) $skill)))
            ->filter()
            ->unique();

        $matches = $jobSkills->intersect($workerSkills)->count();

        return (int) round(($matches / $jobSkills->count()) * 100);
    }

    /**
     * Download application attachment
     */
    public function downloadAttachment($applicationId)
    {
        $user = $this->getUserModel();

        \Log::info('downloadAttachment start', ['applicationId' => $applicationId, 'user' => $user]);
        
        if (!$user || !in_array($user->accounttype, ['client', 'admin'])) {
            \Log::warning('downloadAttachment unauthorized user', ['user' => $user]);
            return response('Unauthorized user', 403);
        }

        // Get the application
        $application = Application::with('jobPosting')->find($applicationId);

        if (!$application) {
            \Log::warning('downloadAttachment application not found', ['applicationId' => $applicationId]);
            return response('Application not found', 404);
        }

        if (!$application->attachments) {
            \Log::warning('downloadAttachment no attachments', ['applicationId' => $applicationId]);
            return response('No attachment available for this application', 404);
        }

        if ($user->accounttype === 'client') {
            $clientId = $user->record_id;
            if (!$application->jobPosting || $application->jobPosting->client_id !== $clientId) {
                \Log::warning('downloadAttachment unauthorized ownership', ['applicationId' => $applicationId, 'applicationClientId' => optional($application->jobPosting)->client_id, 'loggedClientId' => $clientId]);
                return response('Unauthorized for this application', 403);
            }
        }

        if (!Storage::disk('public')->exists($application->attachments)) {
            \Log::warning('downloadAttachment file missing', ['applicationId' => $applicationId, 'attachment' => $application->attachments]);
            abort(404, 'File not found on server');
        }

        $linkType = request()->query('inline', false) ? 'inline' : 'download';
        $extension = strtolower(pathinfo($application->attachments, PATHINFO_EXTENSION));
        $filePath = Storage::disk('public')->path($application->attachments);

        \Log::info('downloadAttachment success', ['applicationId' => $applicationId, 'attachment' => $application->attachments, 'extension' => $extension, 'linkType' => $linkType]);

        if ($extension === 'pdf' && $linkType === 'inline') {
            return response()->file($filePath, ['Content-Type' => 'application/pdf']);
        }

        return Storage::disk('public')->download($application->attachments);
    }

    /**
     * Display all interviews for the authenticated client
     */
    public function interviews()
    {
        $user = $this->getUserModel();
        if (!$user || !in_array($user->accounttype, ['client', 'admin'])) {
            return redirect('/login');
        }

        if ($user->accounttype === 'client') {
            $jobPostings = JobPosting::where('client_id', $user->record_id)->get();
        } else {
            $jobPostings = JobPosting::all();
        }

        $jobPostingIds = $jobPostings->pluck('id');

        $applications = Application::whereIn('job_posting_id', $jobPostingIds)
            ->with(['jobPosting', 'applicant.healthcareWorker.skills', 'applicant.healthcareWorker.employmentHistory'])
            ->latest('created_at')
            ->get();

        $candidates = User::where('accounttype', 'healthcare_worker')->get();

        return view('client-interviews', compact('applications', 'jobPostings', 'candidates'));
    }

    /**
     * Schedule an interview for an application
     */
    public function scheduleInterview(Request $request)
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'interview_date' => 'required|date',
            'interview_location' => 'nullable|string|max:255',
            'interview_notes' => 'nullable|string',
            'additional_notes' => 'nullable|string',
        ]);

        $sessionUser = session('user', []);
        $user = User::find($sessionUser['id'] ?? null);
        
        if (!$user) {
            return redirect('/login')->with('error', 'Unauthorized access');
        }
        
        $application = Application::with('jobPosting')->find($request->application_id);
        
        // Verify that this application belongs to one of the client's job postings
        // if ($application->jobPosting->client_id !== $user->id) {
        //     return redirect()->back()->with('error', 'Unauthorized action');
        // }

        $application->interview_status = 'scheduled';
        $application->interview_date = $validated['interview_date'];
        $application->interview_location = $validated['interview_location'] ?? null;
        $application->interview_notes = $validated['interview_notes'] ?? null;
        $application->additional_notes = $validated['additional_notes'] ?? null;
        $application->save();

        // Here you could send an email to the applicant about the scheduled interview
        // Mail::send('emails.interview-scheduled', [...]);

        return redirect('/' . $this->getRoutePrefix() . '/interviews')->with('success', 'Interview scheduled successfully!');
    }

    /**
     * Reschedule an interview
     */
    public function rescheduleInterview(Request $request)
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'interview_date' => 'required|date',
            'interview_location' => 'nullable|string|max:255',
            'reschedule_reason' => 'nullable|string',
        ]);

        $sessionUser = session('user', []);
        $user = User::find($sessionUser['id'] ?? null);
        
        if (!$user) {
            return redirect('/login')->with('error', 'Unauthorized access');
        }
        
        $application = Application::with('jobPosting')->find($request->application_id);
        
        // Verify that this application belongs to one of the client's job postings
        // if ($application->jobPosting->client_id !== $user->id) {
        //     return redirect()->back()->with('error', 'Unauthorized action');
        // }

        $application->interview_date = $validated['interview_date'];
        $application->interview_location = $validated['interview_location'] ?? null;
        $application->reschedule_reason = $validated['reschedule_reason'] ?? null;
        $application->interview_status = 'rescheduled';
        $application->save();

        // Here you could send an email to the applicant about the rescheduled interview
        // Mail::send('emails.interview-rescheduled', [...]);

        return redirect('/' . $this->getRoutePrefix() . '/interviews')->with('success', 'Interview rescheduled successfully!');
    }

    /**
     * Add notes to an interview
     */
    public function addInterviewNotes(Request $request)
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'interview_notes' => 'required|string',
        ]);

        $sessionUser = session('user', []);
        $user = User::find($sessionUser['id'] ?? null);
        
        if (!$user) {
            return redirect('/login')->with('error', 'Unauthorized access');
        }
        
        $application = Application::with('jobPosting')->find($request->application_id);
        
        // Verify that this application belongs to one of the client's job postings
        // if ($application->jobPosting->user_id !== $user->id) {
        //     return redirect()->back()->with('error', 'Unauthorized action');
        // }

        $application->interview_notes = $validated['interview_notes'];
        $application->save();

        return redirect('/' . $this->getRoutePrefix() . '/interviews')->with('success', 'Interview notes updated successfully!');
    }

    /**
     * Reject an application
     */
    public function rejectApplication($id)
    {
        $sessionUser = session('user', []);
        $user = User::find($sessionUser['id'] ?? null);
        
        if (!$user) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            return redirect('/login')->with('error', 'Unauthorized access');
        }
        
        $application = Application::with(['jobPosting', 'applicant'])->find($id);
        
        if (!$application) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Application not found'], 404);
            }

            return redirect()->back()->with('error', 'Application not found');
        }

        if ($user->accounttype === 'client') {
            $clientId = $user->record_id;
            if (!$application->jobPosting || $application->jobPosting->client_id !== $clientId) {
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized action'], 403);
                }
                return redirect()->back()->with('error', 'Unauthorized action');
            }
        }

        $application->interview_status = 'rejected';
        $application->save();

        $emailSent = false;

        if ($application->applicant?->email) {
            try {
                Mail::send('emails.interview-rejected', [
                    'applicantName' => $application->applicant->fullname ?? 'Applicant',
                    'jobTitle' => $application->jobPosting->title ?? 'the position you applied for',
                ], function ($message) use ($application) {
                    $message->to($application->applicant->email)
                        ->subject('Update on Your Application');
                });

                $emailSent = true;
            } catch (\Throwable $exception) {
                Log::error('Failed to send rejection email.', [
                    'application_id' => $application->id,
                    'applicant_email' => $application->applicant->email,
                    'error' => $exception->getMessage(),
                ]);

                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Application rejected successfully, but the rejection email could not be sent.',
                    ]);
                }

                return redirect()->back()->with('success', 'Application rejected successfully!')
                    ->with('error', 'The rejection email could not be sent. Please check your mail configuration.');
            }
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $emailSent
                    ? 'Application rejected successfully. A rejection email has been sent to the applicant.'
                    : 'Application rejected successfully.',
            ]);
        }

        return redirect()->back()->with('success', $emailSent
            ? 'Application rejected successfully. A rejection email has been sent to the applicant.'
            : 'Application rejected successfully!');
    }

    /**
     * Send a simple test email using the current SMTP configuration.
     */
    public function sendTestEmail(Request $request)
    {
        $sessionUser = session('user', []);
        $user = User::find($sessionUser['id'] ?? null);

        if (!$user || $user->accounttype !== 'client') {
            return redirect('/login')->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'email' => 'nullable|email',
        ]);

        $recipientEmail = $validated['email'] ?? config('mail.from.address');

        try {
            Mail::send('emails.test-email', [
                'recipientEmail' => $recipientEmail,
                'sentAt' => now()->format('M d, Y h:i A'),
                'appName' => config('app.name'),
            ], function ($message) use ($recipientEmail) {
                $message->to($recipientEmail)
                    ->subject('Test Email from Our Care Pty Ltd');
            });
        } catch (\Throwable $exception) {
            Log::error('Failed to send test email.', [
                'recipient_email' => $recipientEmail,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Test email could not be sent: ' . $exception->getMessage());
        }

        return redirect()->back()->with('success', 'Test email sent successfully to ' . $recipientEmail . '.');
    }

    /**
     * Mark an interview as completed
     */
    public function completeInterview($id)
    {
        $sessionUser = session('user', []);
        $user = User::find($sessionUser['id'] ?? null);
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
        }
        
        $application = Application::with('jobPosting')->find($id);
        
        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found'], 404);
        }

        $application->interview_status = 'completed';
        $application->save();

        return redirect('/' . $this->getRoutePrefix() . '/interviews')->with('success', 'Interview marked as completed successfully!');
    }

    /**
     * Mark an interview/application as hired
     */
    public function hireApplicant($id)
    {
        $sessionUser = session('user', []);
        $user = User::find($sessionUser['id'] ?? null);

        if (!$user) {
            return redirect('/login')->with('error', 'Unauthorized access');
        }

        $application = Application::with('jobPosting')->find($id);

        if (!$application) {
            return redirect('/' . $this->getRoutePrefix() . '/finalization')->with('error', 'Application not found');
        }

        $application->interview_status = 'hired';
        $application->save();

        Endorsement::updateOrCreate(
            [
                'worker_id' => $application->user_applied_id,
                'job_post_id' => $application->job_posting_id,
            ],
            [
                'meet_and_greet_date' => $application->interview_date,
                'meet_and_greet_link' => null,
                'endorsed_by' => $user->id,
            ]
        );

        $emailSent = false;

        if ($application->applicant?->email) {
            try {
                Mail::send('emails.interview-hired', [
                    'applicantName' => $application->applicant->fullname ?? 'Applicant',
                    'jobTitle' => $application->jobPosting->title ?? 'the role you applied for',
                ], function ($message) use ($application) {
                    $message->to($application->applicant->email)
                        ->subject('Congratulations on Your Application');
                });

                $emailSent = true;
            } catch (\Throwable $exception) {
                Log::error('Failed to send hired email.', [
                    'application_id' => $application->id,
                    'applicant_email' => $application->applicant->email,
                    'error' => $exception->getMessage(),
                ]);

                return redirect('/' . $this->getRoutePrefix() . '/finalization')
                    ->with('success', 'Applicant marked as hired successfully!')
                    ->with('error', 'The congratulations email could not be sent. Please check your mail configuration.');
            }
        }

        return redirect('/' . $this->getRoutePrefix() . '/finalization')->with('success', $emailSent
            ? 'Applicant marked as hired successfully. A congratulations email has been sent to the applicant.'
            : 'Applicant marked as hired successfully!');
    }

    /**
     * View details of a specific interview/application
     */
    public function interviewDetails($id)
    {
        $sessionUser = session('user', []);
        $user = User::find($sessionUser['id'] ?? null);
        
        if (!$user) {
            return redirect('/login')->with('error', 'Unauthorized access');
        }
        
        $application = Application::with(['jobPosting', 'applicant'])->find($id);
        
        if (!$application) {
            return redirect('/' . $this->getRoutePrefix() . '/interviews')->with('error', 'Interview not found');
        }

        if ($user->accounttype === 'client') {
            if (!$application->jobPosting || $application->jobPosting->client_id !== $user->record_id) {
                return redirect('/' . $this->getRoutePrefix() . '/interviews')->with('error', 'Unauthorized access');
            }
        }

        return view('client-interview-detail', compact('application'));
    }

    /**
     * Display endorsed workers for the client.
     */
    public function endorsedWorkers()
    {
        $user = $this->getUserModel();
        if (!$user || $user->accounttype !== 'client') {
            return redirect('/login');
        }

        $jobPostingIds = JobPosting::where('client_id', $user->record_id)->pluck('id');
        $endorsements = Endorsement::with(['jobPosting', 'worker', 'admin'])
            ->whereIn('job_post_id', $jobPostingIds)
            ->orderByDesc('created_at')
            ->get();

        return view('client-endorsed-workers', compact('endorsements'));
    }

    /**
     * View a worker's profile in modal
     */
    public function viewWorkerProfile(int $workerId)
    {
        $user = $this->getUserModel();
        if (!$user || $user->accounttype !== 'client') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the worker's user record
        $workerUser = User::find($workerId);
        if (!$workerUser || $workerUser->accounttype !== 'healthcare_worker') {
            return response()->json(['error' => 'Worker not found'], 404);
        }

        // Get the worker's profile
        $worker = HealthcareWorker::with(['skills', 'employmentHistory', 'ndisRequirementsCompleted'])
            ->where('user_id', $workerId)
            ->first();

        // Get NDIS requirements
        $ndisRequirements = NdisRequirementParameter::orderBy('requirements')->get();
        $completedRequirements = $worker
            ? $worker->ndisRequirementsCompleted->keyBy('parameter_id')
            : collect();

        // Return as HTML view for modal
        return view('worker-profile-modal', compact(
            'workerUser',
            'worker',
            'ndisRequirements',
            'completedRequirements'
        ));
    }

    protected function getClientOwnedEndorsement(int $endorsementId, User $user): ?Endorsement
    {
        return Endorsement::with(['jobPosting', 'worker', 'admin'])
            ->where('id', $endorsementId)
            ->whereHas('jobPosting', function ($query) use ($user) {
                $query->where('client_id', $user->record_id);
            })
            ->first();
    }

    public function scheduleEndorsementMeetAndGreet(Request $request)
    {
        $user = $this->getUserModel();
        if (!$user || $user->accounttype !== 'client') {
            return redirect('/login');
        }

        $validated = $request->validate([
            'endorsement_id' => 'required|exists:endorsements,id',
            'meet_and_greet_date' => 'required|date',
            'meet_and_greet_link' => 'nullable|string|max:255',
        ]);

        $endorsement = $this->getClientOwnedEndorsement((int) $validated['endorsement_id'], $user);

        if (!$endorsement) {
            return redirect('/client/endorsed-workers')->with('error', 'Endorsement not found or not accessible.');
        }

        $endorsement->meet_and_greet_date = $validated['meet_and_greet_date'];
        $endorsement->meet_and_greet_link = $validated['meet_and_greet_link'] ?? null;
        $endorsement->save();

        return redirect('/client/endorsed-workers')->with('status', 'Meet and greet scheduled successfully.');
    }

    public function rescheduleEndorsementMeetAndGreet(Request $request)
    {
        $user = $this->getUserModel();
        if (!$user || $user->accounttype !== 'client') {
            return redirect('/login');
        }

        $validated = $request->validate([
            'endorsement_id' => 'required|exists:endorsements,id',
            'meet_and_greet_date' => 'required|date',
            'meet_and_greet_link' => 'nullable|string|max:255',
        ]);

        $endorsement = $this->getClientOwnedEndorsement((int) $validated['endorsement_id'], $user);

        if (!$endorsement) {
            return redirect('/client/endorsed-workers')->with('error', 'Endorsement not found or not accessible.');
        }

        $endorsement->meet_and_greet_date = $validated['meet_and_greet_date'];
        $endorsement->meet_and_greet_link = $validated['meet_and_greet_link'] ?? null;
        $endorsement->save();

        return redirect('/client/endorsed-workers')->with('status', 'Meet and greet rescheduled successfully.');
    }

    public function createEndorsement()
    {
        $user = $this->getUserModel();
        if (!$user || $user->accounttype !== 'admin') {
            return redirect('/login');
        }

        $applications = Application::with(['jobPosting', 'applicant'])
            ->whereHas('jobPosting')
            ->whereHas('applicant', function ($query) {
                $query->where('accounttype', 'healthcare_worker');
            })
            ->orderByDesc('created_at')
            ->get();

        $endorsements = Endorsement::with(['jobPosting', 'worker', 'client', 'admin'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin-endorsements-create', compact('applications', 'endorsements'));
    }

    public function storeEndorsement(Request $request)
    {
        $user = $this->getUserModel();
        if (!$user || $user->accounttype !== 'admin') {
            return redirect('/login');
        }

        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
        ]);

        $application = Application::with(['jobPosting', 'applicant'])->find($validated['application_id']);

        if (!$application || !$application->jobPosting || !$application->applicant) {
            return redirect()->back()->with('error', 'Selected application is invalid.');
        }

        $endorsement = Endorsement::updateOrCreate(
            [
                'worker_id' => $application->user_applied_id,
                'job_post_id' => $application->job_posting_id,
            ],
            [
                'client_id' => $application->jobPosting->client_id,
                'endorsed_by' => $user->id,
            ]
        );

        return redirect('/admin/endorsements/create')->with('success', 'Endorsement has been created successfully.');
    }

    /**
     * Create a standalone interview entry (creates/updates an application and schedules the interview).
     */
    public function createInterview(Request $request)
    {
        $validated = $request->validate([
            'job_posting_id' => 'required|exists:job_postings,id',
            'candidate_id' => 'required|exists:users,id',
            'interview_date' => 'required|date',
            'interview_location' => 'nullable|string|max:255',
            'interview_notes' => 'nullable|string',
            'application_details' => 'nullable|string',
            'expected_salary' => 'nullable|numeric',
        ]);

        $sessionUser = session('user', []);
        $user = User::find($sessionUser['id'] ?? null);
        
        if (!$user) {
            return redirect('/login')->with('error', 'Unauthorized access');
        }

        $jobPosting = JobPosting::find($validated['job_posting_id']);
        // if (!$jobPosting || $jobPosting->user_id !== $user->id) {
        //     return redirect()->back()->with('error', 'Unauthorized action');
        // }

        $candidate = User::find($validated['candidate_id']);
        if (!$candidate || $candidate->accounttype !== 'healthcare_worker') {
            return redirect()->back()->with('error', 'Invalid candidate selected');
        }

        $application = Application::firstOrNew(
            [
                'job_posting_id' => $jobPosting->id,
                'user_applied_id' => $candidate->id,
            ],
            [
                'application_details' => $validated['application_details'] ?? null,
                'expected_salary' => $validated['expected_salary'] ?? null,
            ]
        );

        $application->interview_status = 'scheduled';
        $application->interview_date = $validated['interview_date'];
        $application->interview_location = $validated['interview_location'] ?? null;
        $application->interview_notes = $validated['interview_notes'] ?? null;
        $application->save();

        return redirect('/' . $this->getRoutePrefix() . '/interviews')->with('success', 'Interview created successfully!');
    }
}
