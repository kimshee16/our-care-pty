<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Client;
use App\Models\HealthcareWorker;
use App\Models\JobPosting;
use App\Models\JobPostingKeySkill;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class ClientApplicationMatchPercentageTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_applications_page_shows_match_percentage(): void
    {
        if (!Schema::hasTable('job_postings')) {
            Schema::create('job_postings', function (Blueprint $table) {
                $table->id();
            });
        }

        $client = Client::create([
            'first_name' => 'Test',
            'last_name' => 'Client',
            'email' => 'client-match@example.com',
            'phone' => '1234567890',
            'date_of_birth' => '1990-01-01',
            'address' => '123 Main St',
            'city' => 'Sydney',
            'state' => 'NSW',
            'zip_code' => '2000',
            'country' => 'Australia',
        ]);

        $clientUser = User::factory()->create([
            'accounttype' => 'client',
            'record_id' => $client->id,
        ]);

        $applicant = User::factory()->create([
            'accounttype' => 'healthcare_worker',
        ]);

        $worker = HealthcareWorker::create([
            'user_id' => $applicant->id,
            'profession' => 'nurse',
            'location' => 'Sydney, NSW 2000',
        ]);

        Skill::create([
            'workers_id' => $worker->id,
            'skill' => 'ICU Care',
        ]);

        Skill::create([
            'workers_id' => $worker->id,
            'skill' => 'Medication Administration',
        ]);

        $jobPosting = JobPosting::create([
            'title' => 'ICU Nurse',
            'description' => 'Need ICU support.',
            'client_id' => $client->id,
            'location' => 'Sydney, NSW 2000',
        ]);

        DB::table('job_postings')->insert([
            'id' => $jobPosting->id,
        ]);

        JobPostingKeySkill::create([
            'job_posting_id' => $jobPosting->id,
            'skill' => 'ICU Care',
        ]);

        JobPostingKeySkill::create([
            'job_posting_id' => $jobPosting->id,
            'skill' => 'Wound Care',
        ]);

        Application::create([
            'job_posting_id' => $jobPosting->id,
            'user_applied_id' => $applicant->id,
            'application_details' => 'Interested in the role.',
        ]);

        $response = $this->withSession([
            'user' => ['id' => $clientUser->id, 'accounttype' => 'client'],
        ])->get('/client/applications');

        $response->assertOk();
        $response->assertSee('Match %');
        $response->assertSee('50%');
    }
}
