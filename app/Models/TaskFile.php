<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TaskFileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

final class TaskFile extends Model
{
    /** @use HasFactory<TaskFileFactory> */
    use HasFactory;

    public function comment(): BelongsTo
    {
        return $this->belongsTo(TaskComment::class);
    }

    protected static function booted(): void
    {
        self::deleting(function (TaskFile $file): void {
            Storage::delete($file->url);
        });
    }
}
