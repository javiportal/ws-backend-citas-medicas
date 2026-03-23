<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'blood_type' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'allergies' => fake()->optional(0.4)->sentence(),
            'chronic_conditions' => fake()->optional(0.3)->sentence(),
            'current_medications' => fake()->optional(0.3)->sentence(),
            'notes' => fake()->optional(0.5)->paragraph(),
        ];
    }
}