<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A basic test example.
     */

    public function test_login_no_input_route_fails()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/login');
        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The email field is required. (and 1 more error)',
            'errors' => [
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.'],
            ],
        ]);
    }

    public function test_login_wrong_email_password()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/login', [
                    'email' => 'omodamolaoladeji@gmail.com',
                    'password' => 'omodamolaoladeji@gmail.com',
                ]);
        $response->assertStatus(422);
    }
}
