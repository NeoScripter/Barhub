<?php

declare(strict_types=1);

namespace App\Services\Integration;

use App\Models\Person;
use App\Http\Resources\Integration\PersonResource;
use App\Services\Integration\BaseIntegrationService;

class PersonIntegrationService extends BaseIntegrationService
{
    public function create(Person $person): void
    {
        $response = $this->post('/api/external/v2/users/create', PersonResource::make($person));

        if (!$response->successful()) {
            $this->log_error('Не удалось создать докладчика', [
                'person_id' => $person->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Докладчик создан', ['person_id' => $person->id]);
    }

    public function update(Person $person): void
    {
        $response = $this->put('/api/external/v2/users/update/' . $person->id, PersonResource::make($person));

        if (!$response->successful()) {
            $this->log_error('Не удалось отредактировать докладчика', [
                'person_id' => $person->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Докладчик отредактирован', ['person_id' => $person->id]);
    }

    public function destroy(Person $person): void
    {
        $response = $this->delete('/api/external/v2/users/delete/',  ['userIds' => [$person->id]]);

        if (!$response->successful()) {
            $this->log_error('Не удалось удалить докладчика', [
                'person_id' => $person->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Докладчик удален', ['person_id' => $person->id]);
    }
}
