<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $role = $this->data['role'] ?? null;

        if (blank($role)) {
            return;
        }

        Role::findOrCreate($role);

        $this->record->syncRoles([$role]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['role']);
        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['role'] = $this->record->roles->first()?->name;
        return $data;
    }
}
