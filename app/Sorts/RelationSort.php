<?php

declare(strict_types=1);

namespace App\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

final class RelationSort implements Sort
{
    public function __construct(
        private readonly string $table,
        private readonly string $column,
        private readonly string $foreignKey,
    ) {}

    public function __invoke(Builder $query, bool $descending, string $property): void
    {
        $query->leftJoin($this->table, "{$this->table}.id", '=', $query->getModel()->getTable() . ".{$this->foreignKey}")
            ->orderBy("{$this->table}.{$this->column}", $descending ? 'desc' : 'asc')
            ->select($query->getModel()->getTable() . '.*');
    }
}
