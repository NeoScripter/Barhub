<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\TaskStatus;
use App\Events\CompanyCreated;

final class CreateTasksFromTemplates
{
    public function handle(CompanyCreated $event): void
    {
        $company = $event->company;

        $company->exhibition
            ->taskTemplates
            ->each(function ($template) use ($company): void {
                $task = $company->tasks()->create([
                    'title'       => $template->title,
                    'description' => $template->description,
                    'deadline'    => $template->deadline,
                    'status'      => TaskStatus::TO_BE_COMPLETED,
                ]);

                if ($template->comment || $template->file_url) {
                    $comment = $task->comments()->create([
                        'content' => $template->comment ?? null,
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
