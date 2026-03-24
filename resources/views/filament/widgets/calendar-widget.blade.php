<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Header --}}
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <x-filament::button size="sm" color="gray" wire:click="previousMonth">
                ← Anterior
            </x-filament::button>

            <h2 style="font-size: 18px; font-weight: bold; text-transform: capitalize;">
                {{ $this->getMonthName() }}
            </h2>

            <x-filament::button size="sm" color="gray" wire:click="nextMonth">
                Siguiente →
            </x-filament::button>
        </div>

        {{-- Doctor Legend --}}
        @php
            $doctorColors = $this->getDoctorColors();
        @endphp
        <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
            @foreach($doctorColors as $id => $info)
                <div style="display: flex; align-items: center; gap: 6px; font-size: 12px;">
                    <span style="width: 12px; height: 12px; border-radius: 50%; display: inline-block; background-color: {{ $info['color'] }};"></span>
                    <span>{{ $info['name'] }}</span>
                </div>
            @endforeach
        </div>

        {{-- Calendar Grid --}}
        @php
            $days = $this->getDaysInMonth();
            $appointments = $this->getAppointmentsForMonth();
            $schedules = $this->getDoctorSchedules();
            $dayNames = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        @endphp

        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; background-color: rgba(128,128,128,0.3); border-radius: 8px; overflow: hidden;">
            {{-- Day Headers --}}
            @foreach($dayNames as $dayName)
                <div style="padding: 8px; text-align: center; font-size: 12px; font-weight: 600; background-color: rgba(128,128,128,0.15); color: #9ca3af;">
                    {{ $dayName }}
                </div>
            @endforeach

            {{-- Calendar Days --}}
            @foreach($days as $day)
                @php
                    $dateKey = $day['date']->format('Y-m-d');
                    $dayOfWeek = $day['date']->dayOfWeek;
                    $dayAppointments = $appointments->get($dateKey, collect());
                    $daySchedules = $schedules->get($dayOfWeek, collect());
                    $hasAvailability = $daySchedules->isNotEmpty() && $day['isCurrentMonth'];

                    $bgColor = $day['isCurrentMonth'] ? 'rgba(0,0,0,0.2)' : 'rgba(0,0,0,0.4)';
                    $border = $day['isToday'] ? 'box-shadow: inset 0 0 0 2px #f59e0b;' : '';
                    $textColor = $day['isCurrentMonth'] ? '#e5e7eb' : '#4b5563';
                    $dayNumWeight = $day['isToday'] ? 'font-weight: bold; color: #f59e0b;' : '';
                @endphp

                <div style="min-height: 90px; padding: 4px; background-color: {{ $bgColor }}; {{ $border }}">
                    {{-- Day Number --}}
                    <div style="font-size: 12px; font-weight: 500; margin-bottom: 4px; color: {{ $textColor }}; {{ $dayNumWeight }}">
                        {{ $day['date']->day }}
                    </div>

                    {{-- Availability --}}
                    @if($hasAvailability)
                        @foreach($daySchedules as $schedule)
                            @php
                                $color = $doctorColors[$schedule->doctor_id]['color'] ?? '#6b7280';
                                $start = substr($schedule->start_time, 0, 5);
                                $end = substr($schedule->end_time, 0, 5);
                            @endphp
                            <div style="font-size: 9px; line-height: 1.2; border-radius: 3px; padding: 2px 4px; margin-bottom: 2px; color: white; background-color: {{ $color }}80; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $doctorColors[$schedule->doctor_id]['name'] ?? 'Doctor' }}: {{ $start }}-{{ $end }}">
                                {{ $start }}-{{ $end }}
                            </div>
                        @endforeach
                    @endif

                    {{-- Appointments --}}
                    @foreach($dayAppointments->take(2) as $appointment)
                        @php
                            $apptColor = $doctorColors[$appointment->doctor_id]['color'] ?? '#6b7280';
                        @endphp
                        <div style="font-size: 9px; line-height: 1.2; border-radius: 3px; padding: 2px 4px; margin-bottom: 2px; color: white; font-weight: 600; background-color: {{ $apptColor }}; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }} - {{ $appointment->scheduled_at->format('H:i') }}">
                            {{ $appointment->scheduled_at->format('H:i') }} {{ $appointment->patient->first_name }}
                        </div>
                    @endforeach

                    @if($dayAppointments->count() > 2)
                        <div style="font-size: 9px; color: #9ca3af; padding: 0 4px;">
                            +{{ $dayAppointments->count() - 2 }} más
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>