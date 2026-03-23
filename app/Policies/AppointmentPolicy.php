<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('medico')) {
            return $appointment->doctor_id === $user->id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'asistente']);
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('medico')) {
            return $appointment->doctor_id === $user->id;
        }

        return $user->hasAnyRole(['admin', 'asistente']);
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('admin');
    }
}