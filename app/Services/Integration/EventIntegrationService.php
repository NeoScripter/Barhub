<?php

declare(strict_types=1);

namespace App\Services\Integration;

use App\Models\Event;
use App\Http\Resources\Integration\EventResource;
use App\Services\Integration\BaseIntegrationService;

class EventIntegrationService extends BaseIntegrationService
{
    public function create(Event $event): void
    {
        $response = $this->post('/api/external/v2/sessions/create', EventResource::make($event));

        if (!$response->successful()) {
            $this->log_error('Не удалось создать мероприятие', [
                'event_id' => $event->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Мероприятие создано', ['event_id' => $event->id]);
    }

    public function update(Event $event): void
    {
        $response = $this->put('/api/external/v2/sessions/update/' . $event->id, EventResource::make($event));

        if (!$response->successful()) {
            $this->log_error('Не удалось отредактировать мероприятие', [
                'event_id' => $event->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Мероприятие отредактировано', ['event_id' => $event->id]);
    }

    public function destroy(Event $event): void
    {
        $response = $this->delete('/api/external/v2/sessions/delete/' . $event->id, []);

        if (!$response->successful()) {
            $this->log_error('Не удалось удалить мероприятие', [
                'event_id' => $event->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Мероприятие удалено', ['event_id' => $event->id]);
    }
}
