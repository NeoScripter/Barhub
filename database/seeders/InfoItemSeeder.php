<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\InfoItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class InfoItemSeeder extends Seeder
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

        InfoItem::factory()
            ->count(random_int(2, 5))
            ->for($admin->exhibitions()->first(), 'exhibition')
            ->afterCreating(function (InfoItem $infoItem): void {
                $infoItem->image()->create(
                    [
                        'webp3x' => '/storage/people/avatar3x.webp',
                        'webp2x' => '/storage/people/avatar2x.webp',
                        'webp' => '/storage/people/avatar.webp',
                        'avif3x' => '/storage/people/avatar3x.avif',
                        'avif2x' => '/storage/people/avatar2x.avif',
                        'avif' => '/storage/people/avatar.avif',
                        'tiny' => '/storage/people/avatar-tiny.webp',
                        'alt' => "{$infoItem->title}'s alt",
                        'type' => 'image',
                    ]
                );
            })
            ->create();
    }
}
