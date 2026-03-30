<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\TaskTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

final class TaskTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()
            ->whereHas('exhibitions')
            ->where('role', UserRole::ADMIN)
            ->first();

        TaskTemplate::factory()
            ->count(random_int(1, 3))
            ->for($admin, 'user')
            ->for($admin->exhibitions()->first(), 'exhibition')
            ->create();
    }
}
