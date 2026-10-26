<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminClientRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_client_registration_from_the_shared_list(): void
    {
        config(['services.recaptcha.site_key' => 'public-site-key']);
        $admin = User::factory()->create(['accounttype' => 'admin']);
        $this->withSession(['user' => $admin->toArray()]);

        $this->get('/admin-registrations')->assertOk()
            ->assertSee('Add a Client')
            ->assertSee(route('admin.clients.create'));
        $this->get('/admin/clients/create')->assertOk()
            ->assertSee('Create Client Account')
            ->assertSee('Back to registrations')
            ->assertSee('name="privacy_rules_disclaimer"', false)
            ->assertSee('action="' . route('admin.clients.store') . '"', false)
            ->assertDontSee('public-site-key')
            ->assertDontSee('Already have an account?');
    }

    public function test_admin_creates_an_approved_client_and_keeps_their_session(): void
    {
        config(['services.recaptcha.secret_key' => 'public-registration-secret']);
        Http::preventStrayRequests();
        Http::fake();
        Mail::fake();
        $admin = User::factory()->create(['accounttype' => 'admin']);
        $session = $admin->toArray();

        $this->withSession(['user' => $session])
            ->post('/admin/clients', $this->clientData() + [
                'accounttype' => 'admin',
                'approved_by' => 999,
                'email_verified_at' => now()->toDateTimeString(),
            ])
            ->assertRedirect('/admin-registrations')
            ->assertSessionHas('status', 'Client account created and approved successfully.')
            ->assertSessionHas('user', $session);

        $client = Client::where('email', 'new-client@example.com')->sole();
        $user = User::where('email', $client->email)->sole();
        $this->assertSame('client', $user->accounttype);
        $this->assertSame($client->id, (int) $user->record_id);
        $this->assertSame(1, (int) $user->approved);
        $this->assertSame($admin->id, (int) $user->approved_by);
        $this->assertNull($user->email_verified_at);
        $this->assertTrue(Hash::check('ClientPassword123!', $user->password));
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'first_name' => 'Jamie',
            'last_name' => 'Smith',
            'alias' => 'Jamie Care',
            'phone' => '0400000000',
            'date_of_birth' => '1990-01-15',
            'address' => '10 Main Street',
            'city' => 'Sydney',
            'state' => 'NSW',
            'zip_code' => '2000',
            'country' => 'Australia',
        ]);
        $this->get('/admin-registrations')->assertOk()->assertSee('Jamie Smith');
        Http::assertNothingSent();
        Mail::assertNothingSent();

        $this->flushSession();
        $this->post('/login', ['email' => $client->email, 'password' => 'ClientPassword123!'])
            ->assertRedirect('/client-dashboard')
            ->assertSessionHas('user.id', $user->id);
    }

    public function test_guests_and_non_admins_cannot_access_or_submit_the_form(): void
    {
        $this->get('/admin/clients/create')->assertRedirect('/login');
        $this->post('/admin/clients', $this->clientData())->assertRedirect('/login');

        foreach (['client' => '/client-dashboard', 'healthcare_worker' => '/healthcare-jobs'] as $role => $redirect) {
            $user = User::factory()->create(['accounttype' => $role]);
            $this->withSession(['user' => $user->toArray()]);
            $this->get('/admin/clients/create')->assertRedirect($redirect);
            $this->post('/admin/clients', $this->clientData())->assertRedirect($redirect);

            // Check the persisted role even if the session still says admin.
            $this->withSession(['user' => ['id' => $user->id, 'accounttype' => 'admin']]);
            $this->get('/admin/clients/create')->assertForbidden();
            $this->post('/admin/clients', $this->clientData())->assertForbidden();
        }

        $this->assertDatabaseCount('clients', 0);
        $this->assertDatabaseMissing('users', ['email' => 'new-client@example.com']);
    }

    public function test_duplicate_email_is_rejected_without_creating_a_client(): void
    {
        $admin = User::factory()->create(['accounttype' => 'admin']);
        User::factory()->create(['email' => 'new-client@example.com']);

        $this->withSession(['user' => $admin->toArray()])->from('/admin/clients/create')
            ->post('/admin/clients', $this->clientData())
            ->assertRedirect('/admin/clients/create')->assertSessionHasErrors('email');

        $this->assertDatabaseCount('clients', 0);
        $this->assertDatabaseCount('users', 2);
    }

    public function test_invalid_submission_preserves_details_but_not_passwords(): void
    {
        $admin = User::factory()->create(['accounttype' => 'admin']);
        $data = array_replace($this->clientData(), [
            'password_confirmation' => 'DifferentPassword',
            'privacy_rules_disclaimer' => '0',
            'dob' => now()->addDay()->toDateString(),
        ]);

        $this->withSession(['user' => $admin->toArray()])->from('/admin/clients/create')
            ->post('/admin/clients', $data)
            ->assertRedirect('/admin/clients/create')
            ->assertSessionHasErrors(['password', 'privacy_rules_disclaimer', 'dob'])
            ->assertSessionHas('_old_input.address', '10 Main Street')
            ->assertSessionMissing('_old_input.password')
            ->assertSessionMissing('_old_input.password_confirmation');
        $this->get('/admin/clients/create')->assertOk()
            ->assertSee('value="10 Main Street"', false)
            ->assertDontSee('ClientPassword123!');
        $this->assertDatabaseCount('clients', 0);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_profile_creation_is_rolled_back_if_account_creation_fails(): void
    {
        $admin = User::factory()->create(['accounttype' => 'admin']);
        User::creating(function () {
            throw new \RuntimeException('Simulated account creation failure');
        });

        try {
            $this->withSession(['user' => $admin->toArray()])
                ->post('/admin/clients', $this->clientData())->assertStatus(500);
            $this->assertDatabaseCount('clients', 0);
            $this->assertDatabaseCount('users', 1);
        } finally {
            User::flushEventListeners();
        }
    }

    public function test_public_registration_still_uses_its_own_action_and_requires_approval(): void
    {
        config(['services.recaptcha.site_key' => 'public-site-key', 'services.recaptcha.secret_key' => null]);
        $this->get('/client-register')->assertOk()
            ->assertSee('Client Registration')
            ->assertSee('action="' . url('/client-register') . '"', false)
            ->assertSee('public-site-key')
            ->assertSee('Already have an account?')
            ->assertDontSee('Create Client Account');

        $this->post('/client-register', $this->clientData() + ['adminRegistration' => true])
            ->assertRedirect('/login')->assertSessionHasNoErrors();
        $user = User::where('email', 'new-client@example.com')->sole();
        $this->assertSame(0, (int) $user->approved);
        $this->assertNull($user->approved_by);
    }

    protected function clientData(): array
    {
        return [
            'firstName' => 'Jamie',
            'lastName' => 'Smith',
            'alias' => 'Jamie Care',
            'email' => 'new-client@example.com',
            'phone' => '0400000000',
            'dob' => '1990-01-15',
            'address' => '10 Main Street',
            'city' => 'Sydney',
            'state' => 'NSW',
            'zipcode' => '2000',
            'country' => 'Australia',
            'password' => 'ClientPassword123!',
            'password_confirmation' => 'ClientPassword123!',
            'privacy_rules_disclaimer' => '1',
        ];
    }
}
