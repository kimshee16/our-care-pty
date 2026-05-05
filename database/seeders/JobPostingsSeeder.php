<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\User;
use App\Models\JobPosting;
use App\Models\JobPostingKeyRequirement;

class JobPostingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a sample client (employer)
        $client = Client::updateOrCreate(
            ['email' => 'employer@carehub.com'],
            [
                'first_name' => 'City',
                'last_name' => 'General',
                'phone' => '555-0101',
                'date_of_birth' => now()->subYears(30)->toDateString(),
                'address' => '123 Health St',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'country' => 'USA',
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'employer@carehub.com'],
            [
                'fullname' => 'City General Employer',
                'phone' => '555-0101',
                'password' => Hash::make('P@ssw0rd@123'),
                'accounttype' => 'client',
                'record_id' => $client->id,
                'verified' => true,
                'approved' => 1,
            ]
        );

        $jobData = [
            [
                'title' => 'Registered Nurse - ICU',
                'description' => 'We are seeking experienced ICU nurses to join our intensive care unit. The ideal candidate will have strong clinical skills and the ability to work in a fast-paced environment.',
                'minimum_pay_offer' => 65000,
                'maximum_pay_offer' => 75000,
                'location' => 'New York, NY',
                'employment_type' => 'Full-time',
                'experience' => '2+ years',
                'specialty' => 'Critical Care',
                'requirements' => [
                    'Valid RN License',
                    '2+ years of ICU experience',
                    'BLS and ACLS certification',
                    'Excellent communication skills',
                    'Ability to work 12-hour shifts',
                ],
            ],
            [
                'title' => 'General Physician',
                'description' => 'Join our expanding clinic as a General Physician. We offer a collaborative environment with modern facilities and comprehensive patient care.',
                'minimum_pay_offer' => 150000,
                'maximum_pay_offer' => 180000,
                'location' => 'Los Angeles, CA',
                'employment_type' => 'Full-time',
                'experience' => '3+ years',
                'specialty' => 'General Medicine',
                'requirements' => [
                    'MD or DO degree',
                    'Valid medical license',
                    '3+ years of practice experience',
                    'Board certified or board eligible',
                    'Strong diagnostic and treatment skills',
                ],
            ],
        ];

        foreach ($jobData as $data) {
            $job = JobPosting::updateOrCreate(
                [
                    'client_id' => $client->id,
                    'title' => $data['title'],
                ],
                [
                    'description' => $data['description'],
                    'minimum_pay_offer' => $data['minimum_pay_offer'],
                    'maximum_pay_offer' => $data['maximum_pay_offer'],
                    'location' => $data['location'],
                    'employment_type' => $data['employment_type'],
                    'experience' => $data['experience'],
                    'specialty' => $data['specialty'],
                ]
            );

            // Replace requirements
            JobPostingKeyRequirement::where('job_posting_id', $job->id)->delete();
            foreach ($data['requirements'] as $req) {
                JobPostingKeyRequirement::create([
                    'job_posting_id' => $job->id,
                    'description' => $req,
                ]);
            }
        }
    }
}
