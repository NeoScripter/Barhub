<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

final class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Выставка', 'color_hex' => '#F9BBD2'],
            ['name' => 'Турнир', 'color_hex' => '#E4FFA3'],
            ['name' => 'Лекция', 'color_hex' => '#D8DBFF'],
            ['name' => 'Семинар', 'color_hex' => '#fcba03'],
        ];

        foreach ($data as $theme) {
            Theme::factory($theme)->create();
        }
    }
}
