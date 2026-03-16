<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Exhibition;
use Illuminate\Foundation\Events\Dispatchable;

final class ExhibitionCreated
{
    use Dispatchable;

    public function __construct(
        public readonly Exhibition $exhibition,
    ) {}
}
