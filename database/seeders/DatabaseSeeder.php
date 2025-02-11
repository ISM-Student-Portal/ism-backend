<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\Student;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $alumnis = Alumni::get();
        foreach ($alumnis as $alumni) {
            $student = Student::where('email', $alumni->email)->first();
            if ($student !== null) {
                $student->is_alumni = true;
                $student->save();
            }
        }
    }
}
