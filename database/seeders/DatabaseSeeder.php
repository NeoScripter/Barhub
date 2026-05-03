<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seeders = [UserSeeder::class];

        if (app()->environment() === 'local') {
            $seeders = array_merge(
                $seeders,
                [
                    ExhibitionSeeder::class,
                    TagSeeder::class,
                    EventSeeder::class,
                    ThemeSeeder::class,
                    StageSeeder::class,
                    CompanySeeder::class,
                    TaskTemplateSeeder::class,
                    InfoItemSeeder::class,

                ]
            );
        }

        $this->call($seeders);
    }
}
