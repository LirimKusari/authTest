<?php

namespace Database\Seeders;

use App\Authorization\Catalog\PermissionCatalog;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $catalog = app(PermissionCatalog::class);

        foreach ($catalog->definitions() as $definition) {
            Permission::findOrCreate($definition->key, 'web');
        }

        foreach ($catalog->rolePermissions() as $role => $permissions) {
            Role::findOrCreate($role, 'web')->syncPermissions($permissions);
        }
    }
}
