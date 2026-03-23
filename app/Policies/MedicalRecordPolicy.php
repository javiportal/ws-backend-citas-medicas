<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'medico']);
    }

    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->hasAnyRole(['admin', 'medico']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'medico']);
    }

    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->hasAnyRole(['admin', 'medico']);
    }

    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        return false;
    }
}