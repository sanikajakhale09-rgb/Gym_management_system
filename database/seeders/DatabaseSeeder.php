<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Gym Owner']);
        Role::create(['name' => 'Manager']);
        Role::create(['name' => 'Receptionist']);
        Role::create(['name' => 'Trainer']);
    }
}