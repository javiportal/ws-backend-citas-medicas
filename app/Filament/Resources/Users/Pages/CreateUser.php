<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $role = $this->data['role'] ?? null;

        if (blank($role)) {
            return;
        }

        Role::findOrCreate($role);

        $this->record->syncRoles([$role]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['role']);
        return $data;
    }
}
