<?php

declare(strict_types=1);

namespace App\Services\Integration;

use App\Models\Stage;
use App\Http\Resources\Integration\StageResource;
use App\Services\Integration\BaseIntegrationService;

class StageIntegrationService extends BaseIntegrationService
{
    public function create(Stage $stage): void
    {
        $response = $this->post('/api/external/v2/locations/create', StageResource::make($stage));

        if (!$response->successful()) {
            $this->log_error('Не удалось создать локацию', [
                'stage_id' => $stage->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Локация создана', ['stage_id' => $stage->id]);
    }

    public function update(Stage $stage): void
    {
        $response = $this->put('/api/external/v2/locations/update/' . $stage->id, StageResource::make($stage));

        if (!$response->successful()) {
            $this->log_error('Не удалось отредактировать локацию', [
                'stage_id' => $stage->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Локация отредактирована', ['stage_id' => $stage->id]);
    }

    public function destroy(Stage $stage): void
    {
        $response = $this->delete('/api/external/v2/locations/delete/' . $stage->id);

        if (!$response->successful()) {
            $this->log_error('Не удалось удалить локацию', [
                'stage_id' => $stage->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Локация удалена', ['stage_id' => $stage->id]);
    }
}
