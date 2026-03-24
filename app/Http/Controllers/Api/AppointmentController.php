<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function store(StoreAppointmentRequest $request)
    {
        $scheduledAt = Carbon::parse($request->scheduled_at);
        $dayOfWeek = $scheduledAt->dayOfWeek;
        $time = $scheduledAt->format('H:i:s');

        $isWithinSchedule = DoctorSchedule::where('doctor_id', $request->doctor_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>', $time)
            ->exists();

        if (!$isWithinSchedule) {
            return response()->json([
                'message' => 'El médico no tiene disponibilidad en ese horario.',
                'errors' => [
                    'scheduled_at' => ['El médico no atiende en el día u hora seleccionado.'],
                ],
            ], 422);
        }

        $durationMinutes = $request->duration_minutes ?? 30;
        $endTime = $scheduledAt->copy()->addMinutes($durationMinutes);

        $hasConflict = Appointment::where('doctor_id', $request->doctor_id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($scheduledAt, $endTime) {
                $query->where(function ($q) use ($scheduledAt, $endTime) {
                    $q->where('scheduled_at', '>=', $scheduledAt)
                      ->where('scheduled_at', '<', $endTime);
                })->orWhere(function ($q) use ($scheduledAt) {
                    $q->where('scheduled_at', '<=', $scheduledAt)
                      ->whereRaw("scheduled_at + (duration_minutes || ' minutes')::interval > ?", [$scheduledAt]);
                });
            })
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'message' => 'Ya existe una cita en ese horario para este médico.',
                'errors' => [
                    'scheduled_at' => ['El horario seleccionado ya está ocupado.'],
                ],
            ], 422);
        }

        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $durationMinutes,
            'status' => 'scheduled',
            'reason' => $request->reason,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Cita creada exitosamente.',
            'data' => $appointment->load(['patient', 'doctor']),
        ], 201);
    }
}