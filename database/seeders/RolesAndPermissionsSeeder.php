<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'material.view', 'material.create', 'material.edit', 'material.delete',
            'inventory.view', 'inventory.edit',
            'checkout.view', 'checkout.create', 'checkout.return',
            'inspection.view', 'inspection.create',
            'location.view', 'location.manage',
            'category.view', 'category.manage',
            'user.manage',
            'report.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $wart = Role::firstOrCreate(['name' => 'materialwart']);
        $wart->syncPermissions([
            'material.view', 'material.create', 'material.edit',
            'inventory.view', 'inventory.edit',
            'checkout.view', 'checkout.create', 'checkout.return',
            'inspection.view', 'inspection.create',
            'location.view', 'category.view',
            'report.view',
        ]);

        $user = Role::firstOrCreate(['name' => 'user']);
        $user->syncPermissions([
            'material.view',
            'inventory.view',
            'checkout.view', 'checkout.create', 'checkout.return',
            'inspection.view',
            'location.view', 'category.view',
        ]);
    }
}
