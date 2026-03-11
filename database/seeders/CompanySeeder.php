<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Followup;
use App\Models\Service;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskFile;
use App\Models\User;
use Illuminate\Database\Seeder;

final class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $tags = Tag::all();
        $exhibitions = Exhibition::all();

        if ($tags->isEmpty() || $exhibitions->isEmpty()) {
            $this->command->error('Missing required data. Run Tag, and Exhibition seeders first.');

            return;
        }

        $exhibitions->each(
            fn (Exhibition $exhibition) => Company::factory()
                ->has(
                    Task::factory()
                        ->count(8)
                        ->has(TaskComment::factory()
                            ->when(
                                $exhibition->users()->first(),
                                fn ($factory, $user) => $factory->for($user)
                            )
                            ->has(TaskFile::factory(), 'file')
                            ->count(random_int(1, 3)), 'comments')
                )
                ->has(Service::factory()
                    ->count(5))
                ->count(10)
                ->for($exhibition)
                ->hasAttached($tags->random(random_int(1, 3)))
                ->afterCreating(function (Company $company): void {

                    $company->logo()->create([
                        'webp3x' => '/storage/people/logo3x.webp',
                        'webp2x' => '/storage/people/logo2x.webp',
                        'webp' => '/storage/people/logo.webp',
                        'avif3x' => '/storage/people/logo3x.avif',
                        'avif2x' => '/storage/people/logo2x.avif',
                        'avif' => '/storage/people/logo.avif',
                        'tiny' => '/storage/people/logo-tiny.webp',
                        'alt' => 'alt',
                        'type' => 'logo',
                    ]);

                    $company->services()->each(function ($service) use ($company): void {
                        $data = User::factory()->make([
                            'role' => UserRole::EXPONENT->value,
                        ])->getAttributes();

                        $user = $company->users()->firstOrCreate(
                            ['email' => $data['email']],
                            $data
                        );
                        Followup::factory([
                            'user_id' => $user->id,
                            'service_id' => $service->id,
                        ])->create();
                    });
                })
                ->create()
        );
    }
}
