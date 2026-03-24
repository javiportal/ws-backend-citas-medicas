<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class CalendarWidget extends Widget
{
    protected string $view = 'filament.widgets.calendar-widget';

    protected int | string | array $columnSpan = 'full';

    public int $year;
    public int $month;

    public function mount(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function getMonthName(): string
    {
        return Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y');
    }

    public function getDaysInMonth(): array
    {
        $startOfMonth = Carbon::create($this->year, $this->month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        $days = [];
        $current = $startOfCalendar->copy();

        while ($current <= $endOfCalendar) {
            $days[] = [
                'date' => $current->copy(),
                'isCurrentMonth' => $current->month === $this->month,
                'isToday' => $current->isToday(),
            ];
            $current->addDay();
        }

        return $days;
    }

    public function getAppointmentsForMonth(): Collection
    {
        $startOfMonth = Carbon::create($this->year, $this->month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $query = Appointment::with(['patient', 'doctor'])
            ->whereBetween('scheduled_at', [$startOfMonth, $endOfMonth])
            ->where('status', '!=', 'cancelled');

        if (auth()->user() && auth()->user()->hasRole('medico')) {
            $query->where('doctor_id', auth()->id());
        }

        return $query->get()->groupBy(function ($appointment) {
            return $appointment->scheduled_at->format('Y-m-d');
        });
    }

    public function getDoctorSchedules(): Collection
    {
        $query = DoctorSchedule::with('doctor');

        if (auth()->user() && auth()->user()->hasRole('medico')) {
            $query->where('doctor_id', auth()->id());
        }

        return $query->get()->groupBy('day_of_week');
    }

    public function getDoctorColors(): array
    {
        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
        $doctors = User::role('medico')->pluck('name', 'id')->toArray();
        $result = [];
        $i = 0;

        foreach ($doctors as $id => $name) {
            $result[$id] = [
                'name' => $name,
                'color' => $colors[$i % count($colors)],
            ];
            $i++;
        }

        return $result;
    }
}