<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Client;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminApplicationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_numeric_text_and_missing_salary_expectations(): void
    {
        if (! Schema::hasTable('job_postings')) {
            Schema::create('job_postings', function (Blueprint $table) {
                $table->id();
            });
        }

        $admin = User::factory()->create(['accounttype' => 'admin']);
        $client = Client::create([
            'first_name' => 'Test',
            'last_name' => 'Client',
            'email' => 'applications-client@example.com',
            'phone' => '1234567890',
            'date_of_birth' => '1990-01-01',
            'address' => '123 Main St',
            'city' => 'Sydney',
            'state' => 'NSW',
            'zip_code' => '2000',
            'country' => 'Australia',
        ]);
        $job = JobPosting::create([
            'title' => 'Support Worker',
            'description' => 'Support worker application test.',
            'client_id' => $client->id,
        ]);
        DB::table('job_postings')->insert(['id' => $job->id]);

        $expectedDisplays = collect();
        foreach ([
            ['65000.50', '$65,000.50'],
            ['0', '$0.00'],
            ['$40–$50/hour & travel', '$40–$50/hour & travel'],
            ['   ', 'N/A'],
            [null, 'N/A'],
        ] as [$salary, $display]) {
            $user = User::factory()->create(['accounttype' => 'healthcare_worker']);
            $application = Application::create([
                'job_posting_id' => $job->id,
                'user_applied_id' => $user->id,
                'expected_salary' => $salary,
            ]);
            $expectedDisplays->put($application->id, $display);
        }

        $this->withSession(['user' => ['id' => $admin->id, 'accounttype' => 'admin']])
            ->get('/admin/applications')
            ->assertOk()
            ->assertSee('$65,000.50')
            ->assertSee('$0.00')
            ->assertSee('$40–$50/hour &amp; travel', false)
            ->assertViewHas('applications', fn ($applications) => $applications
                ->pluck('expected_salary_display', 'id')->sortKeys()->all() === $expectedDisplays->all());
    }
}
