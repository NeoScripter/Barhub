<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     */
    public function run(): void
    {

        if (app()->environment() === 'local') {

            User::factory()->create([
                'name' => 'User',
                'email' => 'user@gmail.com',
                'role' => UserRole::USER->value,
            ]);

            User::factory()->create([
                'name' => 'exponent',
                'email' => 'exponent@gmail.com',
                'role' => UserRole::EXPONENT->value,
            ]);

            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'role' => UserRole::ADMIN->value,
            ]);
        }

        User::factory()->create([
            'name' => 'Геннадий',
            'email' => 'gena-bar@mail.ru',
            'role' => UserRole::SUPER_ADMIN->value,
        ]);

        User::factory(4)->create();
    }
}
