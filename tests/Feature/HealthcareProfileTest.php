<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\HealthcareWorker;
use Illuminate\Support\Facades\Hash;

class HealthcareProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_requires_login()
    {
        $response = $this->get('/healthcare-profile');
        $response->assertRedirect('/login');
    }

    public function test_healthcare_user_can_view_and_edit_profile()
    {
        // create a healthcare user
        $user = User::factory()->create([
            'password' => Hash::make('secret'),
            'accounttype' => 'healthcare_worker',
            'verified' => true,
            'approved' => 1,
        ]);

        // create an associated worker profile
        $worker = HealthcareWorker::create([
            'user_id' => $user->id,
            'profession' => 'nurse',
        ]);

        // simulate session login
        $response = $this->withSession(['user' => ['id' => $user->id, 'accounttype' => 'healthcare_worker']])
                         ->get('/healthcare-profile');
        $response->assertStatus(200);
        $response->assertSee($user->fullname);
        $response->assertSee('nurse');

        // post update
        $response = $this->withSession(['user' => ['id' => $user->id, 'accounttype' => 'healthcare_worker']])
                         ->post('/healthcare-profile', [
                             'profession' => 'doctor',
                         ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('healthcare_workers', ['user_id' => $user->id, 'profession' => 'doctor']);
    }
}
