<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Constants\ExhibitionName;
use App\Enums\UserRole;
use App\Models\Exhibition;
use App\Models\Service;
use App\Models\User;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Seeder;

final class ExhibitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (ExhibitionName::NAMES as $name) {
            Exhibition::factory()->has(Service::factory(rand(3, 5)))->create(['name' => $name]);
        }

        $admin = User::query()->where('role', UserRole::ADMIN)->first();
        $admin->exhibitions()->attach(Exhibition::query()->first()->id);
    }
}
