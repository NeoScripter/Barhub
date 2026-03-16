<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Company;
use Illuminate\Foundation\Events\Dispatchable;

final class CompanyCreated
{
    use Dispatchable;

    public function __construct(
        public readonly Company $company,
    ) {}
}
