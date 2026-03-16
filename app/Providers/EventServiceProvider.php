<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\CompanyCreated;
use App\Events\ExhibitionCreated;
use App\Listeners\CreateDefaultTaskTemplate;
use App\Listeners\CreateTasksFromTemplates;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

final class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ExhibitionCreated::class => [
            CreateDefaultTaskTemplate::class,
        ],
        CompanyCreated::class => [
            CreateTasksFromTemplates::class,
        ],
    ];
}
