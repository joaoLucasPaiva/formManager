<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissions
        $permissions = [
            // Form permissions
            'create_forms',
            'edit_forms',
            'delete_forms',
            'publish_forms',
            'view_forms',
            'view_own_forms',

            // Submission permissions
            'view_submissions',
            'view_own_submissions',
            'export_submissions',
            'delete_submissions',

            // User management permissions
            'manage_users',
            'view_users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // OWNER - Dona (acesso total)
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $owner->syncPermissions(Permission::all());

        // EMPLOYEE - Funcionária (acesso limitado)
        $employee = Role::firstOrCreate(['name' => 'employee']);
        $employee->syncPermissions([
            'create_forms',
            'edit_forms',
            'view_own_forms',
            'view_own_submissions',
            'export_submissions',
        ]);

        // VIEWER - Apenas leitura
        $viewer = Role::firstOrCreate(['name' => 'viewer']);
        $viewer->syncPermissions([
            'view_forms',
            'view_submissions',
            'export_submissions',
        ]);
    }
}
