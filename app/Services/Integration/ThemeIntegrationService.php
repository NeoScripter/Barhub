<?php

declare(strict_types=1);

namespace App\Services\Integration;

use App\Http\Resources\Integration\ThemeResource;
use App\Models\Theme;

class ThemeIntegrationService extends BaseIntegrationService
{
    public function create(Theme $theme): bool
    {
        $response = $this->post('/api/external/v2/tags/create', ThemeResource::make($theme));

        if (!$response->successful()) {
            $this->log_error('Не удалось создать тег', [
                'theme_id' => $theme->id,
                'error'    => $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Тег создан', ['theme_id' => $theme->id]);

        return true;
    }

    public function update(Theme $theme): bool
    {
        $response = $this->put('/api/external/v2/tags/update/' . $theme->id, ThemeResource::make($theme));

        if (!$response->successful()) {
            $this->log_error('Не удалось отредактировать тег', [
                'theme_id' => $theme->id,
                'error'    => $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Тег отредактирован', ['theme_id' => $theme->id]);

        return true;
    }

    /**
     * Обновить, а если записи в Eventicious ещё нет — создать.
     */
    public function sync(Theme $theme): bool
    {
        return $this->update($theme) || $this->create($theme);
    }

    public function destroy(int $themeId): bool
    {
        $response = $this->delete('/api/external/v2/tags/delete/' . $themeId);

        // 404 = в Eventicious его и так нет, считаем удаление успешным.
        if (!$response->successful() && $response->status() !== 404) {
            $this->log_error('Не удалось удалить тег', [
                'theme_id' => $themeId,
                'error'    => $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Тег удален', ['theme_id' => $themeId]);

        return true;
    }
}
