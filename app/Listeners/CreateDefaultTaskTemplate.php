<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ExhibitionCreated;

final class CreateDefaultTaskTemplate
{
    public function handle(ExhibitionCreated $event): void
    {
        $event->exhibition->taskTemplates()->create([
            'title'       => 'Заполнить информацию о компании',
            'description' => 'Пожалуйста, заполните всю необходимую информацию о вашей компании.',
            'deadline'    => $event->exhibition->starts_at,
        ]);
    }
}
