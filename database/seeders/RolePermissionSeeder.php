<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    private array $permissions = [
        'companies.view',
        'companies.create',
        'companies.update',
        'companies.delete',
        'users.view',
        'users.create',
        'users.update',
        'users.delete',
    ];

    /**
     * Seed the application's roles and permissions.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->permissions as $name) {
            Permission::findOrCreate($name, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::findOrCreate('admin', 'web')->syncPermissions($this->permissions);

        Role::findOrCreate('gestor', 'web')->syncPermissions([
            'companies.view',
            'users.view',
        ]);

        foreach (['comercial', 'operacao', 'financeiro'] as $role) {
            Role::findOrCreate($role, 'web')->syncPermissions([]);
        }
    }
}
