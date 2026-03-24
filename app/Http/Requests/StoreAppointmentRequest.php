<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'sometimes|integer|min:15|max:120',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'El paciente es obligatorio.',
            'patient_id.exists' => 'El paciente no existe.',
            'doctor_id.required' => 'El médico es obligatorio.',
            'doctor_id.exists' => 'El médico no existe.',
            'scheduled_at.required' => 'La fecha y hora son obligatorias.',
            'scheduled_at.after' => 'La cita debe ser en una fecha futura.',
        ];
    }
}