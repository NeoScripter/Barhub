<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\TaskStatus;
use App\Events\TaskTemplateCreated;

final class CreateTaskFromTemplate
{
    public function handle(TaskTemplateCreated $event): void
    {
        $template = $event->template;
        $exhibition = $template->exhibition;

        $exhibition->companies()->each(function ($company) use ($template): void {
            $task = $company->tasks()->create([
                'title'       => $template->title,
                'description' => $template->description,
                'deadline'    => $template->deadline,
                'status'      => $template->status ?? TaskStatus::TO_BE_COMPLETED->value,
            ]);

            if ($template->comment) {
                $comment = $task->comments()->create([
                    'content' => $template->comment,
                    'user_id' => $template->user?->id ?? null
                ]);

                if ($template->file_url) {
                    $comment->file()->create([
                        'url'  => $template->file_url,
                        'name' => $template->file_name,
                    ]);
                }
            }
        });
    }
}
