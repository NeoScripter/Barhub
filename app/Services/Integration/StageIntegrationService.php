<?php

declare(strict_types=1);

namespace App\Services\Integration;

use App\Http\Resources\Integration\StageResource;
use App\Models\Stage;

class StageIntegrationService extends BaseIntegrationService
{
    public function create(Stage $stage): bool
    {
        $response = $this->post('/api/external/v2/locations/create', StageResource::make($stage));

        if (!$response->successful()) {
            $this->log_error('Не удалось создать локацию', [
                'stage_id' => $stage->id,
                'error'    => $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Локация создана', ['stage_id' => $stage->id]);

        return true;
    }

    public function update(Stage $stage): bool
    {
        $response = $this->put('/api/external/v2/locations/update/' . $stage->id, StageResource::make($stage));

        if (!$response->successful()) {
            $this->log_error('Не удалось отредактировать локацию', [
                'stage_id' => $stage->id,
                'error'    => $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Локация отредактирована', ['stage_id' => $stage->id]);

        return true;
    }

    /**
     * Обновить, а если записи в Eventicious ещё нет — создать.
     */
    public function sync(Stage $stage): bool
    {
        return $this->update($stage) || $this->create($stage);
    }

    public function destroy(int $stageId): bool
    {
        $response = $this->delete('/api/external/v2/locations/delete/' . $stageId);

        // 404 = в Eventicious её и так нет, считаем удаление успешным.
        if (!$response->successful() && $response->status() !== 404) {
            $this->log_error('Не удалось удалить локацию', [
                'stage_id' => $stageId,
                'error'    => $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Локация удалена', ['stage_id' => $stageId]);

        return true;
    }
}
