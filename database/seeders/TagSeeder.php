<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

final class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Экпонент'],
            ['name' => 'Генеральный партнер'],
            ['name' => 'Инфопартнер'],
        ];

        foreach ($data as $tag) {
            Tag::factory($tag)->create();
        }
    }
}
