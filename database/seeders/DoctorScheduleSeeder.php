<?php

namespace Database\Seeders;

use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = User::role('medico')->get();

        $schedules = [
            [
                ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '11:00'],
                ['day_of_week' => 3, 'start_time' => '08:00', 'end_time' => '11:00'],
                ['day_of_week' => 4, 'start_time' => '08:00', 'end_time' => '11:00'],
                ['day_of_week' => 6, 'start_time' => '15:00', 'end_time' => '17:00'],
            ],
            [
                ['day_of_week' => 2, 'start_time' => '09:00', 'end_time' => '12:00'],
                ['day_of_week' => 5, 'start_time' => '09:00', 'end_time' => '12:00'],
                ['day_of_week' => 1, 'start_time' => '14:00', 'end_time' => '17:00'],
            ],
            [
                ['day_of_week' => 1, 'start_time' => '10:00', 'end_time' => '13:00'],
                ['day_of_week' => 2, 'start_time' => '10:00', 'end_time' => '13:00'],
                ['day_of_week' => 3, 'start_time' => '10:00', 'end_time' => '13:00'],
                ['day_of_week' => 4, 'start_time' => '10:00', 'end_time' => '13:00'],
                ['day_of_week' => 5, 'start_time' => '10:00', 'end_time' => '13:00'],
            ],
            [
                ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '11:00'],
                ['day_of_week' => 3, 'start_time' => '08:00', 'end_time' => '11:00'],
                ['day_of_week' => 2, 'start_time' => '14:00', 'end_time' => '17:00'],
                ['day_of_week' => 4, 'start_time' => '14:00', 'end_time' => '17:00'],
            ],
            [
                ['day_of_week' => 2, 'start_time' => '09:00', 'end_time' => '12:00'],
                ['day_of_week' => 4, 'start_time' => '09:00', 'end_time' => '12:00'],
                ['day_of_week' => 6, 'start_time' => '09:00', 'end_time' => '13:00'],
            ],
        ];

        foreach ($doctors as $index => $doctor) {
            $doctorSchedules = $schedules[$index % count($schedules)];

            foreach ($doctorSchedules as $schedule) {
                DoctorSchedule::create([
                    'doctor_id' => $doctor->id,
                    ...$schedule,
                ]);
            }
        }
    }
}