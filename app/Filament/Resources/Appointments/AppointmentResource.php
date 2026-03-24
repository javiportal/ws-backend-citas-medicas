<?php

namespace App\Filament\Resources\Appointments;

use App\Filament\Resources\Appointments\Pages;
use App\Models\Appointment;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Citas';

    protected static ?string $modelLabel = 'Cita';

    protected static ?string $pluralModelLabel = 'Citas';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos de la Cita')
                    ->schema([
                        Forms\Components\Select::make('patient_id')
                            ->label('Paciente')
                            ->relationship('patient', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record): string => "{$record->first_name} {$record->last_name}")
                            ->searchable(['first_name', 'last_name'])
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('doctor_id')
                            ->label('Médico')
                            ->options(User::role('medico')->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Fecha y Hora')
                            ->required()
                            ->seconds(false),
                        Forms\Components\Select::make('duration_minutes')
                            ->label('Duración (minutos)')
                            ->options([
                                15 => '15 min',
                                30 => '30 min',
                                45 => '45 min',
                                60 => '60 min',
                            ])
                            ->default(30)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'scheduled' => 'Programada',
                                'completed' => 'Completada',
                                'cancelled' => 'Cancelada',
                            ])
                            ->default('scheduled')
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Detalles')
                    ->schema([
                        Forms\Components\Textarea::make('reason')
                            ->label('Motivo de la cita')
                            ->rows(2)
                            ->maxLength(500),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient_full_name')
                    ->label('Paciente')
                    ->state(fn (Appointment $record): string => $record->patient?->full_name ?? 'Sin paciente')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('patient', function (Builder $patientQuery) use ($search): void {
                            $patientQuery
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('Médico')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Duración')
                    ->suffix(' min'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'Programada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Motivo')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('scheduled_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'scheduled' => 'Programada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ]),
                Tables\Filters\SelectFilter::make('doctor_id')
                    ->label('Médico')
                    ->options(User::role('medico')->pluck('name', 'id'))
                    ->visible(fn (): bool => ! (auth()->user()?->hasRole('medico') ?? false)),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->visible(fn (): bool => auth()->user()?->hasRole('admin') ?? false),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->hasRole('admin') ?? false),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->hasRole('medico')) {
            $query->where('doctor_id', auth()->id());
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'view' => Pages\ViewAppointment::route('/{record}'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
