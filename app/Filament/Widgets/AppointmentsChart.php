<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AppointmentsChart extends ChartWidget
{
protected ?string $heading = 'Citas por Día (Últimos 7 días)';
    protected function getData(): array
    {
        $data = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'date' => $date->format('d/m'),
                'count' => Appointment::whereDate('scheduled_at', $date)->count(),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Citas',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}