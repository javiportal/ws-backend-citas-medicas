<?php

namespace Database\Seeders;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::factory()->count(25)->create();

        $patients->each(function (Patient $patient) {
            MedicalRecord::factory()->create([
                'patient_id' => $patient->id,
            ]);
        });
    }
}