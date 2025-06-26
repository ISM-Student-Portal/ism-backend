<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Student;
use Database\Factories\StudentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranscriptTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */

    public function test_student_transcript_api_route_works()
    {
        $student = Student::factory()->create();
        $this->actingAs($student, 'sanctum');

        $this->assertAuthenticated('sanctum');

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/get-transcript');
        // $response->dd();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'transcript' => [
            ],
        ]);


    }




}
