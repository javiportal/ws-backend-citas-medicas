<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorScheduleFactory extends Factory
{
    public function definition(): array
    {
        $startHour = fake()->randomElement(['08:00', '09:00', '10:00', '14:00', '15:00']);
        $endHour = date('H:i', strtotime($startHour . ' +3 hours'));

        return [
            'doctor_id' => User::factory(),
            'day_of_week' => fake()->numberBetween(1, 6),
            'start_time' => $startHour,
            'end_time' => $endHour,
        ];
    }
}