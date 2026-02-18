<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasFilterSearch
{
    public function scopeWithSearch(Builder $query, string $field, ?string $search): Builder
    {
        return $query->when($search, fn(Builder $q) => $q->where($field, 'like', "%{$search}%"));
    }
}
