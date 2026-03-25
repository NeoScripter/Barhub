<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\TaskTemplateCreated;
use Database\Factories\TaskTemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

final class TaskTemplate extends Model
{
    /** @use HasFactory<TaskTemplateFactory> */
    use HasFactory;

    protected $dispatchesEvents = [
        'created' => TaskTemplateCreated::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exhibition(): BelongsTo
    {
        return $this->belongsTo(Exhibition::class);
    }

    protected static function booted(): void
    {
        self::deleting(function (TaskTemplate $taskTemplate): void {
            if ($taskTemplate->file_url) {
                Storage::delete($taskTemplate->file_url);
            }
        });
    }
}
