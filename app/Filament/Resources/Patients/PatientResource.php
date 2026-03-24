<?php

namespace App\Filament\Resources\Patients;

use App\Filament\Resources\Patients\Pages;
use App\Models\Patient;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pacientes';

    protected static ?string $modelLabel = 'Paciente';

    protected static ?string $pluralModelLabel = 'Pacientes';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Personal')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Apellido')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Fecha de Nacimiento')
                            ->required()
                            ->maxDate(now()),
                        Forms\Components\Select::make('gender')
                            ->label('Género')
                            ->options([
                                'male' => 'Masculino',
                                'female' => 'Femenino',
                                'other' => 'Otro',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Dirección')
                            ->rows(2),
                        Forms\Components\TextInput::make('emergency_contact_name')
                            ->label('Contacto de Emergencia')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('emergency_contact_phone')
                            ->label('Teléfono de Emergencia')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Apellido')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono'),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->label('Fecha Nac.')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Género')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'male' => 'Masculino',
                        'female' => 'Femenino',
                        'other' => 'Otro',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Género')
                    ->options([
                        'male' => 'Masculino',
                        'female' => 'Femenino',
                        'other' => 'Otro',
                    ]),
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

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Personal')
                    ->schema([
                        Infolists\Components\TextEntry::make('first_name')->label('Nombre'),
                        Infolists\Components\TextEntry::make('last_name')->label('Apellido'),
                        Infolists\Components\TextEntry::make('email')->label('Email'),
                        Infolists\Components\TextEntry::make('phone')->label('Teléfono'),
                        Infolists\Components\TextEntry::make('date_of_birth')->label('Fecha de Nacimiento')->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('gender')
                            ->label('Género')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'male' => 'Masculino',
                                'female' => 'Femenino',
                                'other' => 'Otro',
                            }),
                    ])
                    ->columns(3),
                Section::make('Contacto de Emergencia')
                    ->schema([
                        Infolists\Components\TextEntry::make('emergency_contact_name')->label('Nombre'),
                        Infolists\Components\TextEntry::make('emergency_contact_phone')->label('Teléfono'),
                        Infolists\Components\TextEntry::make('address')->label('Dirección'),
                    ])
                    ->columns(3),
                Section::make('Expediente Clínico')
                    ->schema([
                        Infolists\Components\TextEntry::make('medicalRecord.blood_type')->label('Tipo de Sangre'),
                        Infolists\Components\TextEntry::make('medicalRecord.allergies')->label('Alergias')->default('Sin alergias registradas'),
                        Infolists\Components\TextEntry::make('medicalRecord.chronic_conditions')->label('Condiciones Crónicas')->default('Ninguna'),
                        Infolists\Components\TextEntry::make('medicalRecord.current_medications')->label('Medicamentos Actuales')->default('Ninguno'),
                        Infolists\Components\TextEntry::make('medicalRecord.notes')->label('Notas')->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn (): bool => auth()->user()?->hasAnyRole(['admin', 'medico']) ?? false),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'view' => Pages\ViewPatient::route('/{record}'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
