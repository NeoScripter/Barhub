<?php

declare(strict_types=1);

namespace App\Sorts;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

final readonly class ManyToManySort implements Sort
{
    public function __construct(
        private string $pivotTable,
        private string $relatedTable,
        private string $pivotForeignKey,   // e.g. 'company_id'
        private string $pivotRelatedKey,   // e.g. 'tag_id'
        private string $column,            // e.g. 'name'
    ) {}

    public function __invoke(Builder $query, bool $descending, string $property): void
    {
        $modelTable = $query->getModel()->getTable();

        $alreadyJoined = collect($query->getQuery()->joins ?? [])
            ->pluck('table')
            ->contains($this->relatedTable);

        if (! $alreadyJoined) {
            $query
                ->leftJoin($this->pivotTable, "{$modelTable}.id", '=', "{$this->pivotTable}.{$this->pivotForeignKey}")
                ->leftJoin($this->relatedTable, "{$this->pivotTable}.{$this->pivotRelatedKey}", '=', "{$this->relatedTable}.id")
                ->groupBy("{$modelTable}.id");
        }

        $query
            ->orderByRaw("MIN({$this->relatedTable}.{$this->column}) " . ($descending ? 'DESC' : 'ASC'))
            ->select("{$modelTable}.*");
    }
}
