<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HealthcareLocationSuggestionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_healthcare_location_suggestions_are_available_without_login(): void
    {
        Http::fake([
            'https://australiansuburbs.au/api/lookup_suburb*' => Http::response([
                [
                    'locality' => 'Sydney',
                    'state' => 'NSW',
                    'postcode' => '2000',
                ],
            ], 200),
        ]);

        $response = $this->getJson('/healthcare/location-suggestions?q=syd');

        $response->assertOk()
            ->assertJsonFragment([
                'suburb' => 'Sydney',
                'state' => 'NSW',
                'postcode' => '2000',
                'label' => 'Sydney, NSW 2000',
            ]);
    }
}
