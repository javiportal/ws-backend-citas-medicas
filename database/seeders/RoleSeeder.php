<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage-users',
            'view-patients',
            'create-patients',
            'edit-patients',
            'delete-patients',
            'view-medical-records',
            'edit-medical-records',
            'view-appointments',
            'create-appointments',
            'edit-appointments',
            'delete-appointments',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        Role::create(['name' => 'admin'])
            ->givePermissionTo(Permission::all());

        Role::create(['name' => 'medico'])
            ->givePermissionTo([
                'view-patients',
                'view-medical-records',
                'edit-medical-records',
                'view-appointments',
                'edit-appointments',
            ]);

        Role::create(['name' => 'asistente'])
            ->givePermissionTo([
                'view-patients',
                'create-patients',
                'edit-patients',
                'view-appointments',
                'create-appointments',
                'edit-appointments',
            ]);
    }
}