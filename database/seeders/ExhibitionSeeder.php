<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Exhibition;
use Illuminate\Database\Seeder;

final class ExhibitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exhibition::factory(30)->create();
    }
}
