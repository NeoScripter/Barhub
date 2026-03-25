<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\TaskTemplate;
use Illuminate\Foundation\Events\Dispatchable;

final class TaskTemplateCreated
{
    use Dispatchable;

    public function __construct(
        public readonly TaskTemplate $template,
    ) {}
}
