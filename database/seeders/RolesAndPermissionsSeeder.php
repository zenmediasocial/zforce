<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage team',
            'view team',
            'manage children',
            'view children',
            'manage activities',
            'play games',
            'view stats',
            'manage account',
            'access admin',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $superAdmin = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        $teamAdmin = Role::create(['name' => 'team-admin', 'guard_name' => 'web']);
        $teamAdmin->givePermissionTo([
            'manage team',
            'view team',
            'manage children',
            'view children',
            'manage activities',
            'view stats',
            'manage account',
        ]);

        $player = Role::create(['name' => 'player', 'guard_name' => 'web']);
        $player->givePermissionTo([
            'play games',
            'view stats',
            'manage account',
        ]);
    }
}
