<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\TaskTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()
            ->where('role', UserRole::ADMIN)
            ->first();

        TaskTemplate::factory()
            ->count(rand(2, 5))
            ->for($admin, 'user')
            ->create();
    }
}
