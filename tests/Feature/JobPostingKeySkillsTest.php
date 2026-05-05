<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostingKeySkillsTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_create_job_posting_with_catalog_key_skills(): void
    {
        $client = Client::create([
            'first_name' => 'Test',
            'last_name' => 'Client',
            'email' => 'client@example.com',
            'phone' => '1234567890',
            'date_of_birth' => '1990-01-01',
            'address' => '123 Main St',
            'city' => 'Manila',
            'state' => 'Metro Manila',
            'zip_code' => '1000',
            'country' => 'Philippines',
        ]);

        $user = User::factory()->create([
            'accounttype' => 'client',
            'record_id' => $client->id,
        ]);

        $response = $this->withSession([
            'user' => ['id' => $user->id, 'accounttype' => 'client'],
        ])->post('/client/job-postings', [
            'title' => 'ICU Nurse',
            'description' => 'Need an experienced ICU nurse.',
            'specialty' => 'Critical Care',
            'key_skills' => ['ICU Care', 'Medication Administration', 'ICU Care'],
            'requirements' => "Registered nurse\nNight shifts",
        ]);

        $response->assertRedirect('/client/job-postings');

        $job = JobPosting::first();

        $this->assertNotNull($job);
        $this->assertDatabaseHas('job_posting_key_skills', [
            'job_posting_id' => $job->id,
            'skill' => 'ICU Care',
        ]);
        $this->assertDatabaseHas('job_posting_key_skills', [
            'job_posting_id' => $job->id,
            'skill' => 'Medication Administration',
        ]);
        $this->assertDatabaseCount('job_posting_key_skills', 2);
    }

    public function test_job_posting_rejects_key_skills_outside_catalog(): void
    {
        $client = Client::create([
            'first_name' => 'Test',
            'last_name' => 'Client',
            'email' => 'client2@example.com',
            'phone' => '0987654321',
            'date_of_birth' => '1991-01-01',
            'address' => '456 Main St',
            'city' => 'Quezon City',
            'state' => 'Metro Manila',
            'zip_code' => '1100',
            'country' => 'Philippines',
        ]);

        $user = User::factory()->create([
            'accounttype' => 'client',
            'record_id' => $client->id,
        ]);

        $response = $this->from('/client/job-postings/create')
            ->withSession([
                'user' => ['id' => $user->id, 'accounttype' => 'client'],
            ])->post('/client/job-postings', [
                'title' => 'Ward Nurse',
                'description' => 'Ward coverage needed.',
                'key_skills' => ['not-a-real-skill'],
            ]);

        $response->assertRedirect('/client/job-postings/create');
        $response->assertSessionHasErrors('key_skills.0');
        $this->assertDatabaseCount('job_posting_key_skills', 0);
    }
}
