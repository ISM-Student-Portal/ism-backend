<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Student;
use Database\Factories\StudentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
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
    public function test_student_login_successful()
    {
        $student = Student::factory()->create();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/login', [
                    'email' => $student->email,
                    'password' => 'password',
                ]);
        $response->assertStatus(200);
        dump($response);

    }

    public function test_login_wrong_email_password()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/login', [
                    'email' => 'omodamolaoladeji@gmail.com',
                    'password' => 'omodamolaoladeji@gmail.com',
                ]);
        $response->assertStatus(401);
    }
}
