<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = User::role('medico')->get();
        $patients = Patient::all();
        $createdCount = 0;

        while ($createdCount < 25) {
            $doctor = $doctors->random();
            $patient = $patients->random();

            $schedule = DoctorSchedule::where('doctor_id', $doctor->id)->inRandomOrder()->first();

            if (!$schedule) {
                continue;
            }

            $date = Carbon::now()->addDays(rand(1, 28));

            while ($date->dayOfWeek !== $schedule->day_of_week) {
                $date->addDay();
            }

            $startHour = (int) substr($schedule->start_time, 0, 2);
            $endHour = (int) substr($schedule->end_time, 0, 2);
            $hour = rand($startHour, $endHour - 1);
            $minutes = fake()->randomElement([0, 30]);

            $scheduledAt = $date->copy()->setTime($hour, $minutes);

            $conflict = Appointment::where('doctor_id', $doctor->id)
                ->where('scheduled_at', $scheduledAt)
                ->exists();

            if ($conflict) {
                continue;
            }

            Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => fake()->randomElement([15, 30, 45, 60]),
                'status' => fake()->randomElement(['scheduled', 'completed', 'cancelled']),
                'reason' => fake()->sentence(),
                'notes' => fake()->optional(0.5)->paragraph(),
            ]);

            $createdCount++;
        }
    }
}