<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use App\Models\JobPosting;
use App\Models\JobPostingKeyRequirement;
use App\Models\JobPostingKeySkill;
use App\Models\User;

class JobPostingController extends Controller
{
    protected function getClientIdFromSession(bool $requireApproved = false): ?int
    {
        $sessionUser = session('user');
        $user = $sessionUser ? User::find($sessionUser['id']) : null;
        if (!$user || $user->accounttype !== 'client') {
            return null;
        }

        if ($requireApproved && $user->approved != 1) {
            return null;
        }

        return $user->record_id;
    }

    public function index()
    {
        $clientId = $this->getClientIdFromSession();
        if (!$clientId) {
            abort(403);
        }

        $jobs = JobPosting::with(['requirements', 'keySkills'])
            ->where('client_id', $clientId)
            ->orderByDesc('created_at')
            ->get();

        return view('client-job-postings-index', compact('jobs'));
    }

    public function create()
    {
        $clientId = $this->getClientIdFromSession(true);
        if (!$clientId) {
            return redirect('/client-dashboard')->with('error', 'Your account is pending admin approval. You cannot create job postings until your account has been approved.');
        }

        return view('client-job-posting-create');
    }

    public function store(Request $request)
    {
        $healthcareSkillOptions = config('healthcare_skills.options', []);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'minimum_pay_offer' => 'nullable|numeric|min:0',
            'maximum_pay_offer' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:100',
            'experience' => 'nullable|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'key_skills' => 'nullable|array',
            'key_skills.*' => ['nullable', 'string', 'max:100', Rule::in($healthcareSkillOptions)],
            'requirements' => 'nullable|string',
        ]);

        $clientId = $this->getClientIdFromSession(true);
        if (!$clientId) {
            return redirect('/client-dashboard')->with('error', 'Your account is pending admin approval. You cannot create job postings until your account has been approved.');
        }

        $job = JobPosting::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'minimum_pay_offer' => $data['minimum_pay_offer'] ?? null,
            'maximum_pay_offer' => $data['maximum_pay_offer'] ?? null,
            'client_id' => $clientId,
            'location' => $data['location'] ?? null,
            'employment_type' => $data['employment_type'] ?? null,
            'experience' => $data['experience'] ?? null,
            'specialty' => $data['specialty'] ?? null,
        ]);

        $this->saveRequirements($job->id, $data['requirements'] ?? '');
        $this->saveKeySkills($job->id, $data['key_skills'] ?? []);

        return redirect('/client/job-postings')->with('status', 'Job posting created successfully.');
    }

    public function edit($id)
    {
        $clientId = $this->getClientIdFromSession();
        if (!$clientId) {
            abort(403);
        }

        $job = JobPosting::with(['requirements', 'keySkills'])->where('client_id', $clientId)->findOrFail($id);

        $requirements = $job->requirements->pluck('description')->implode("\n");
        $keySkills = $job->keySkills->pluck('skill')->toArray();

        return view('client-job-posting-edit', compact('job', 'requirements', 'keySkills'));
    }

    public function update(Request $request, $id)
    {
        $healthcareSkillOptions = config('healthcare_skills.options', []);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'minimum_pay_offer' => 'nullable|numeric|min:0',
            'maximum_pay_offer' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:100',
            'experience' => 'nullable|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'key_skills' => 'nullable|array',
            'key_skills.*' => ['nullable', 'string', 'max:100', Rule::in($healthcareSkillOptions)],
            'requirements' => 'nullable|string',
        ]);

        $clientId = $this->getClientIdFromSession();
        if (!$clientId) {
            abort(403);
        }

        $job = JobPosting::where('client_id', $clientId)->findOrFail($id);

        $job->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'minimum_pay_offer' => $data['minimum_pay_offer'] ?? null,
            'maximum_pay_offer' => $data['maximum_pay_offer'] ?? null,
            'location' => $data['location'] ?? null,
            'employment_type' => $data['employment_type'] ?? null,
            'experience' => $data['experience'] ?? null,
            'specialty' => $data['specialty'] ?? null,
        ]);

        $this->saveRequirements($job->id, $data['requirements'] ?? '');
        $this->saveKeySkills($job->id, $data['key_skills'] ?? []);

        return redirect('/client/job-postings')->with('status', 'Job posting updated successfully.');
    }

    public function destroy($id)
    {
        $clientId = $this->getClientIdFromSession();
        if (!$clientId) {
            abort(403);
        }

        $job = JobPosting::where('client_id', $clientId)->findOrFail($id);
        $job->delete();

        return redirect('/client/job-postings')->with('status', 'Job posting deleted successfully.');
    }

    protected function saveRequirements(int $jobId, string $requirementsText)
    {
        JobPostingKeyRequirement::where('job_posting_id', $jobId)->delete();

        $requirements = collect(explode("\n", $requirementsText))
            ->map(fn($line) => trim($line))
            ->filter()
            ->unique();

        foreach ($requirements as $requirement) {
            JobPostingKeyRequirement::create([
                'job_posting_id' => $jobId,
                'description' => $requirement,
            ]);
        }
    }

    protected function saveKeySkills(int $jobId, array $keySkills): void
    {
        JobPostingKeySkill::where('job_posting_id', $jobId)->delete();

        $skills = collect($keySkills)
            ->map(fn($skill) => trim((string) $skill))
            ->filter()
            ->unique();

        foreach ($skills as $skill) {
            JobPostingKeySkill::create([
                'job_posting_id' => $jobId,
                'skill' => $skill,
            ]);
        }
    }
}
