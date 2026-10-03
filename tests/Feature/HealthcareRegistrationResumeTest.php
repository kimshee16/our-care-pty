<?php

namespace Tests\Feature;

use App\Models\HealthcareWorker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HealthcareRegistrationResumeTest extends TestCase
{
    use RefreshDatabase;

    public function test_healthcare_worker_can_sign_up_with_pdf_resume(): void
    {
        Storage::fake('public');

        $response = $this->post('/healthcare-register', $this->validRegistrationData([
            'resume' => UploadedFile::fake()->create('resume.pdf', 128, 'application/pdf'),
        ]));

        $response->assertRedirect('/login');

        $worker = HealthcareWorker::first();

        $this->assertNotNull($worker);
        $this->assertNotNull($worker->resume_path);
        Storage::disk('public')->assertExists($worker->resume_path);
        $this->assertStringStartsWith('resumes/', $worker->resume_path);
    }

    public function test_healthcare_worker_resume_must_be_a_pdf(): void
    {
        Storage::fake('public');

        $response = $this->from('/healthcare-register')->post('/healthcare-register', $this->validRegistrationData([
            'resume' => UploadedFile::fake()->create('resume.txt', 32, 'text/plain'),
        ]));

        $response->assertRedirect('/healthcare-register');
        $response->assertSessionHasErrors('resume');
        $this->assertDatabaseCount('users', 0);
        Storage::disk('public')->assertMissing('resumes/resume.txt');
    }

    public function test_admin_can_download_worker_signup_resume(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('resumes/signup-resume.pdf', 'PDF contents');

        $admin = User::factory()->create(['accounttype' => 'admin']);
        $workerUser = User::factory()->create([
            'fullname' => 'Ava Nguyen',
            'accounttype' => 'healthcare_worker',
        ]);

        HealthcareWorker::create([
            'user_id' => $workerUser->id,
            'profession' => 'nurse',
            'resume_path' => 'resumes/signup-resume.pdf',
        ]);

        $response = $this->withSession([
            'user' => ['id' => $admin->id, 'accounttype' => 'admin'],
        ])->get('/admin-registrations/' . $workerUser->id . '/resume');

        $response->assertOk();
        $response->assertHeader('content-disposition', 'attachment; filename=ava-nguyen-resume.pdf');
    }

    private function validRegistrationData(array $overrides = []): array
    {
        return array_merge([
            'firstName' => 'Ava',
            'lastName' => 'Nguyen',
            'email' => 'ava.nguyen@example.test',
            'phone' => '0400000000',
            'profession' => 'nurse',
            'specialization' => 'Aged Care',
            'licenseNumber' => 'NURSE-123',
            'experience' => 5,
            'facility' => 'Our Care Clinic',
            'facilityAddress' => '10 Care Street',
            'location' => 'Sydney NSW',
            'credentials' => 'Registered nurse with community care experience.',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ], $overrides);
    }
}
