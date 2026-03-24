<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pacientes', Patient::count())
                ->description('Registrados en el sistema')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Citas Hoy', Appointment::whereDate('scheduled_at', today())->count())
                ->description('Programadas para hoy')
                ->icon('heroicon-o-calendar')
                ->color('success'),

            Stat::make('Médicos Activos', User::role('medico')->where('is_active', true)->count())
                ->description('Disponibles')
                ->icon('heroicon-o-heart')
                ->color('warning'),

            Stat::make('Citas Pendientes', Appointment::where('status', 'scheduled')->count())
                ->description('Por atender')
                ->icon('heroicon-o-clock')
                ->color('danger'),
        ];
    }
}