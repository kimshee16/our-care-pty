<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'fullname' => 'Admin One',
                'email' => 'admin1@carehub.com',
            ],
            [
                'fullname' => 'Admin Two',
                'email' => 'admin2@carehub.com',
            ],
            [
                'fullname' => 'Admin Three',
                'email' => 'admin3@carehub.com',
            ],
        ];

        foreach ($admins as $admin) {
            DB::table('users')->updateOrInsert(
                ['email' => $admin['email']],
                [
                    'fullname' => $admin['fullname'],
                    'password' => Hash::make('P@ssw0rd@123'),
                    'accounttype' => 'admin',
                    'record_id' => null,
                    'verified' => true,
                    'approved' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
