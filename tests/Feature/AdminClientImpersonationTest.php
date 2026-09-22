<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminClientImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_sign_in_as_client_update_profile_and_return(): void
    {
        $admin = User::factory()->create([
            'fullname' => 'Admin One',
            'accounttype' => 'admin',
        ]);

        $client = Client::create([
            'first_name' => 'Kim',
            'last_name' => 'Ramirez',
            'email' => 'kim@example.com',
            'phone' => '0400000000',
            'date_of_birth' => '1990-01-01',
            'address' => '1 Main Street',
            'city' => 'Sydney',
            'state' => 'NSW',
            'zip_code' => '2000',
            'country' => 'Australia',
        ]);

        $clientUser = User::factory()->create([
            'fullname' => 'Kim Ramirez',
            'email' => 'kim@example.com',
            'phone' => '0400000000',
            'accounttype' => 'client',
            'record_id' => $client->id,
            'approved' => 1,
        ]);

        $this->withSession(['user' => $admin->toArray()])
            ->post("/admin-registrations/{$clientUser->id}/impersonate-client")
            ->assertRedirect('/profile')
            ->assertSessionHas('admin_impersonator');

        $this->assertSame($clientUser->id, session('user.id'));
        $this->assertSame('client', session('user.accounttype'));

        $this->get('/profile')
            ->assertOk()
            ->assertSee('Client Profile')
            ->assertSee('Kim Ramirez');

        $this->post('/profile', [
            'first_name' => 'Kim',
            'last_name' => 'Updated',
            'alias' => 'Kim U',
            'email' => 'kim.updated@example.com',
            'phone' => '0499999999',
            'date_of_birth' => '1990-01-01',
            'address' => '2 Main Street',
            'city' => 'Melbourne',
            'state' => 'VIC',
            'zip_code' => '3000',
            'country' => 'Australia',
        ])->assertRedirect('/profile');

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'last_name' => 'Updated',
            'email' => 'kim.updated@example.com',
            'city' => 'Melbourne',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $clientUser->id,
            'fullname' => 'Kim Updated',
            'email' => 'kim.updated@example.com',
            'phone' => '0499999999',
        ]);

        $this->post('/admin/impersonation/stop')
            ->assertRedirect('/admin-registrations')
            ->assertSessionMissing('admin_impersonator');

        $this->assertSame($admin->id, session('user.id'));
        $this->assertSame('admin', session('user.accounttype'));
    }
}
