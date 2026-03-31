<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskStatus;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
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

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    #[Scope]
    protected function forExhibition(Builder $query, int $exhibitionId): Builder
    {
        return $query
            ->join('companies', 'tasks.company_id', '=', 'companies.id')
            ->where('companies.exhibition_id', $exhibitionId)
            ->select('tasks.*');
    }

    #[Scope]
    protected function forSummary(Builder $query, int $exhibitionId): Collection
    {
        return $query
            ->select(['tasks.status', DB::raw('count(*) as count')])
            ->join('companies', 'companies.id', '=', 'tasks.company_id')
            ->where('companies.exhibition_id', $exhibitionId)
            ->where('tasks.status', '!=', TaskStatus::COMPLETED)
            ->groupBy('tasks.status')
            ->get()
            ->map(fn($task): array => [
                'count' => $task->count,
                'status' => $task->status->label(),
                'rawStatus' => $task->status
            ]);
    }

    #[Scope]
    protected function forExponent(Builder $query, int $companyId): Collection
    {
        return $query
            ->select(['tasks.status', 'tasks.id', 'tasks.title', DB::raw('count(*) as count')])
            ->join('companies', 'companies.id', '=', 'tasks.company_id')
            ->where('companies.id', $companyId)
            ->groupBy('tasks.status')
            ->get()
            ->map(fn($task): array => [
                'id' => $task->id,
                'count' => $task->count,
                'title' => $task->title,
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

    protected static function booted(): void
    {
        self::deleting(function (Task $task): void {
            $task->comments()->each(fn($comment) => $comment->delete());
        });
    }
}
