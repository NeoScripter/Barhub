<?php

namespace Database\Seeders;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => UserPermission::ACCESS_ADMIN_PANEL->value]);
        Permission::create(['name' => UserPermission::MANAGE_EXHIBITIONS->value]);
        Permission::create(['name' => UserPermission::MANAGE_EXHIBITION->value]);

        $superAdminRole = Role::create(['name' => UserRole::SUPER_ADMIN->value]);
        $superAdminRole->givePermissionTo(UserPermission::ACCESS_ADMIN_PANEL);
        $superAdminRole->givePermissionTo(UserPermission::MANAGE_EXHIBITIONS);

        $adminRole = Role::create(['name' => UserRole::ADMIN->value]);
        $adminRole->givePermissionTo(UserPermission::ACCESS_ADMIN_PANEL);

        $exponentRole = Role::create(['name' => UserRole::EXPONENT->value]);
        $userRole = Role::create(['name' => UserRole::USER->value]);

        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@gmail.com',
        ]);
        $user->assignRole($userRole);

        $exponent = User::factory()->create([
            'name' => 'exponent',
            'email' => 'exponent@gmail.com',
        ]);
        $exponent->assignRole($exponentRole);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
        ]);
        $admin->assignRole($adminRole);

        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super-admin@gmail.com',
        ]);
        $superAdmin->assignRole($superAdminRole);
    }
}
