<?php

declare(strict_types=1);

namespace App\Jobs\Integration;

use App\Models\Company;
use App\Models\Event;
use App\Models\Integration;
use App\Models\Person;
use App\Models\Stage;
use App\Models\Theme;
use App\Services\Integration\CompanyIntegrationService;
use App\Services\Integration\EventIntegrationService;
use App\Services\Integration\PersonIntegrationService;
use App\Services\Integration\StageIntegrationService;
use App\Services\Integration\ThemeIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Массовая синхронизация всех сущностей с Eventicious одной джобой —
 * так порядок (справочники → люди → сессии) гарантирован при любом
 * количестве воркеров очереди.
 */
class FullSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 1;

    public int $timeout = 3600;

    public function handle(
        ThemeIntegrationService $themes,
        StageIntegrationService $stages,
        CompanyIntegrationService $companies,
        PersonIntegrationService $people,
        EventIntegrationService $events,
    ): void {
        if (!(bool) Integration::firstOrCreate()->status) {
            Log::channel('integration')->warning('[Eventicious] Массовая синхронизация пропущена: интеграция выключена');

            return;
        }

        $exhibitionId = config('services.eventicious.exhibition_id');
        $report = [];

        $report['темы'] = $this->syncAll(
            Theme::query()
                ->when($exhibitionId, fn ($q) => $q->where('exhibition_id', (int) $exhibitionId))
                ->get(),
            fn (Theme $theme) => $themes->sync($theme),
        );

        $report['залы'] = $this->syncAll(
            Stage::query()
                ->when($exhibitionId, fn ($q) => $q->where('exhibition_id', (int) $exhibitionId))
                ->get(),
            fn (Stage $stage) => $stages->sync($stage),
        );

        // Скрытые с сайта компании удаляем из приложения, остальные выгружаем
        $report['экспоненты'] = $this->syncAll(
            Company::query()
                ->when($exhibitionId, fn ($q) => $q->where('exhibition_id', (int) $exhibitionId))
                ->get(),
            fn (Company $company) => $company->show_on_site
                ? $companies->sync($company)
                : $companies->destroy($company->id),
        );

        $report['спикеры'] = $this->syncAll(
            Person::query()
                ->when($exhibitionId, fn ($q) => $q->whereHas(
                    'exhibitions',
                    fn ($sub) => $sub->whereKey((int) $exhibitionId),
                ))
                ->get(),
            fn (Person $person) => $people->sync($person),
        );

        $report['расписание'] = $this->syncAll(
            Event::query()
                ->when($exhibitionId, fn ($q) => $q->where('exhibition_id', (int) $exhibitionId))
                ->get(),
            fn (Event $event) => $events->sync($event),
        );

        $summary = collect($report)
            ->map(fn (array $r, string $name) => "{$name}: {$r['ok']} ок, {$r['failed']} с ошибкой")
            ->implode('; ');

        Log::channel('integration')->info("[Eventicious] Массовая синхронизация завершена — {$summary}");
    }

    /**
     * @return array{ok: int, failed: int}
     */
    private function syncAll(iterable $models, callable $sync): array
    {
        $ok = 0;
        $failed = 0;

        foreach ($models as $model) {
            try {
                $sync($model) ? $ok++ : $failed++;
            } catch (\Throwable $e) {
                $failed++;
                Log::channel('integration')->error('[Eventicious] Исключение при массовой синхронизации', [
                    'model' => $model::class,
                    'id'    => $model->getKey(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return ['ok' => $ok, 'failed' => $failed];
    }
}
