<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'), // password
            'phone' => $this->faker->phoneNumber(),
            'matric_no' => $this->faker->unique()->randomNumber(8),
            'country' => $this->faker->country(),
            'city' => $this->faker->city(),
            'education' => $this->faker->randomElement(['High School', 'Bachelor', 'Master']),
            
            //
        ];
    }

    public function activated()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }
}
