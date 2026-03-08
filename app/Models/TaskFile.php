<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TaskFileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TaskFile extends Model
{
    /** @use HasFactory<TaskFileFactory> */
    use HasFactory;

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
