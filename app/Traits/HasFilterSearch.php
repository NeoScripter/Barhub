<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasFilterSearch
{
    protected function scopeWithSearch(Builder $query, string $field, ?string $search): Builder
    {
        return $query->when($search, fn (Builder $q) => $q->where($field, 'like', "%{$search}%"));
    }
}
