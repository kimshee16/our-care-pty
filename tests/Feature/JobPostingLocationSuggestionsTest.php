<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JobPostingLocationSuggestionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_fetch_australian_location_suggestions(): void
    {
        Http::fake([
            'https://australiansuburbs.au/api/lookup_suburb*' => Http::response([
                [
                    'locality' => 'Southbank',
                    'state' => 'VIC',
                    'postcode' => '3006',
                ],
                [
                    'locality' => 'Parramatta',
                    'state' => 'NSW',
                    'postcode' => '2150',
                ],
            ], 200),
        ]);

        $client = Client::create([
            'first_name' => 'Test',
            'last_name' => 'Client',
            'email' => 'locations@example.com',
            'phone' => '1234567890',
            'date_of_birth' => '1990-01-01',
            'address' => '123 Main St',
            'city' => 'Sydney',
            'state' => 'NSW',
            'zip_code' => '2000',
            'country' => 'Australia',
        ]);

        $user = User::factory()->create([
            'accounttype' => 'client',
            'record_id' => $client->id,
        ]);

        $response = $this->withSession([
            'user' => ['id' => $user->id, 'accounttype' => 'client'],
        ])->getJson('/client/job-postings/location-suggestions?q=sou');

        $response->assertOk()
            ->assertJsonFragment([
                'suburb' => 'Southbank',
                'state' => 'VIC',
                'postcode' => '3006',
                'label' => 'Southbank, VIC 3006',
            ]);
    }

    public function test_location_suggestions_skip_remote_call_for_short_queries(): void
    {
        Http::fake();

        $client = Client::create([
            'first_name' => 'Test',
            'last_name' => 'Client',
            'email' => 'locations2@example.com',
            'phone' => '1234567891',
            'date_of_birth' => '1990-01-01',
            'address' => '456 Main St',
            'city' => 'Melbourne',
            'state' => 'VIC',
            'zip_code' => '3000',
            'country' => 'Australia',
        ]);

        $user = User::factory()->create([
            'accounttype' => 'client',
            'record_id' => $client->id,
        ]);

        $response = $this->withSession([
            'user' => ['id' => $user->id, 'accounttype' => 'client'],
        ])->getJson('/client/job-postings/location-suggestions?q=s');

        $response->assertOk()->assertExactJson([]);
        Http::assertNothingSent();
    }
}
