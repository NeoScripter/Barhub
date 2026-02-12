<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Stage;
use Illuminate\Database\Seeder;

final class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Площадка 1'],
            ['name' => 'Площадка 2'],
            ['name' => 'Площадка 3'],
            ['name' => 'Площадка 4'],
        ];

        foreach ($data as $theme) {
            Stage::factory($theme)->create();
        }
    }
}
