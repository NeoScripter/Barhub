<?php

declare(strict_types=1);

namespace App\Services\Integration;

use App\Models\Theme;
use App\Http\Resources\Integration\ThemeResource;
use App\Services\Integration\BaseIntegrationService;

class ThemeIntegrationService extends BaseIntegrationService
{
    public function create(Theme $theme): void
    {
        $response = $this->post('/api/external/v2/tags/create', ThemeResource::make($theme));

        if (!$response->successful()) {
            $this->log_error('Не удалось создать тег', [
                'theme_id' => $theme->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Тег создан', ['theme_id' => $theme->id]);
    }

    public function update(Theme $theme): void
    {
        $response = $this->put('/api/external/v2/tags/update/' . $theme->id, ThemeResource::make($theme));

        if (!$response->successful()) {
            $this->log_error('Не удалось отредактировать тег', [
                'theme_id' => $theme->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Тег отредактирован', ['theme_id' => $theme->id]);
    }

    public function destroy(Theme $theme): void
    {
        $response = $this->delete('/api/external/v2/tags/delete/' . $theme->id);

        if (!$response->successful()) {
            $this->log_error('Не удалось удалить тег', [
                'theme_id' => $theme->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Тег удален', ['theme_id' => $theme->id]);
    }
}
