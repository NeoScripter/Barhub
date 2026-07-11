<?php

declare(strict_types=1);

namespace App\Services\Integration;

use App\Http\Resources\Integration\EventResource;
use App\Models\Event;

class EventIntegrationService extends BaseIntegrationService
{
    public function create(Event $event): bool
    {
        $response = $this->post('/api/external/v2/sessions/create', EventResource::make($event));

        if (!$response->successful()) {
            $this->log_error('Не удалось создать мероприятие', [
                'event_id' => $event->id,
                'error'    => $this->explainSessionError($response->status()) ?? $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Мероприятие создано', ['event_id' => $event->id]);

        return true;
    }

    public function update(Event $event): bool
    {
        $response = $this->put('/api/external/v2/sessions/update/' . $event->id, EventResource::make($event));

        if (!$response->successful()) {
            $this->log_error('Не удалось отредактировать мероприятие', [
                'event_id' => $event->id,
                'error'    => $this->explainSessionError($response->status()) ?? $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Мероприятие отредактировано', ['event_id' => $event->id]);

        return true;
    }

    /**
     * Обновить, а если записи в Eventicious ещё нет — создать.
     */
    public function sync(Event $event): bool
    {
        return $this->update($event) || $this->create($event);
    }

    public function destroy(int $eventId): bool
    {
        $response = $this->delete('/api/external/v2/sessions/delete/' . $eventId, []);

        // 404 = в Eventicious его и так нет, считаем удаление успешным.
        if (!$response->successful() && $response->status() !== 404) {
            $this->log_error('Не удалось удалить мероприятие', [
                'event_id' => $eventId,
                'error'    => $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Мероприятие удалено', ['event_id' => $eventId]);

        return true;
    }

    /**
     * API отвечает на конфликт расписания голым 409 без тела — переводим
     * в понятное сообщение (проверено на тестовом событии).
     */
    private function explainSessionError(int $status): ?string
    {
        return $status === 409
            ? 'HTTP 409: дата сессии вне дней проведения события в Eventicious (проверьте даты события в админке Eventicious)'
            : null;
    }
}
