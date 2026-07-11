<?php

declare(strict_types=1);

namespace App\Services\Integration;

use App\Http\Resources\Integration\PersonResource;
use App\Models\Person;

class PersonIntegrationService extends BaseIntegrationService
{
    public function create(Person $person): bool
    {
        // API работает с участниками только батчами: {"users": [...]}
        $response = $this->post('/api/external/v2/users/create', [
            'users' => [PersonResource::make($person)],
        ]);

        if (!$response->successful()) {
            $this->log_error('Не удалось создать докладчика', [
                'person_id' => $person->id,
                'error'     => $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Докладчик создан', ['person_id' => $person->id]);

        return true;
    }

    public function update(Person $person): bool
    {
        $response = $this->patch('/api/external/v2/users/update', [
            'users' => [PersonResource::make($person)],
        ]);

        if (!$response->successful()) {
            $this->log_error('Не удалось отредактировать докладчика', [
                'person_id' => $person->id,
                'error'     => $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Докладчик отредактирован', ['person_id' => $person->id]);

        return true;
    }

    /**
     * Обновить, а если записи в Eventicious ещё нет — создать.
     * Используется массовой синхронизацией и update-джобами.
     */
    public function sync(Person $person): bool
    {
        return $this->update($person) || $this->create($person);
    }

    public function destroy(int $personId): bool
    {
        $response = $this->delete('/api/external/v2/users/delete', [
            'userIds' => [$personId],
        ]);

        // 404 = в Eventicious её и так нет, считаем удаление успешным.
        if (!$response->successful() && $response->status() !== 404) {
            $this->log_error('Не удалось удалить докладчика', [
                'person_id' => $personId,
                'error'     => $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Докладчик удален', ['person_id' => $personId]);

        return true;
    }
}
