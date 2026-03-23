<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => User::factory(),
            'scheduled_at' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'duration_minutes' => fake()->randomElement([15, 30, 45, 60]),
            'status' => fake()->randomElement(['scheduled', 'completed', 'cancelled']),
            'reason' => fake()->sentence(),
            'notes' => fake()->optional(0.5)->paragraph(),
        ];
    }
}