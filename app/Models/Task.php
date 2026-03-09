<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskStatus;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    public function files(): HasMany
    {
        return $this->hasMany(TaskFile::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForExhibition(Builder $query, int $exhibitionId): Collection
    {
        return $query
            ->select(['tasks.status', DB::raw('count(*) as count')])
            ->join('companies', 'companies.id', '=', 'tasks.company_id')
            ->where('companies.exhibition_id', $exhibitionId)
            ->where('tasks.status', '!=', TaskStatus::COMPLETED)
            ->groupBy('tasks.status')
            ->get()
            ->map(fn($task): array => [
                'count'  => $task->count,
                'status' => $task->status->label(),
            ]);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
        ];
    }
}
