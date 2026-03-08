<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TaskCommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TaskComment extends Model
{
    /** @use HasFactory<TaskCommentFactory> */
    use HasFactory;

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
