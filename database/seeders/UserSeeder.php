<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@clinica.com',
        ]);
        $admin->assignRole('admin');

        $doctorNames = [
            'Dr. Carlos López',
            'Dra. María García',
            'Dr. Roberto Hernández',
            'Dra. Ana Martínez',
            'Dr. José Rodríguez',
        ];

        foreach ($doctorNames as $name) {
            $doctor = User::factory()->create(['name' => $name]);
            $doctor->assignRole('medico');
        }

        $assistantNames = [
            'Laura Sánchez',
            'Pedro Ramírez',
            'Sofía Torres',
        ];

        foreach ($assistantNames as $name) {
            $assistant = User::factory()->create(['name' => $name]);
            $assistant->assignRole('asistente');
        }
    }
}