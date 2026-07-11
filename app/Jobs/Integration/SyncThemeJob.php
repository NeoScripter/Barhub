<?php

declare(strict_types=1);

namespace App\Jobs\Integration;

use App\Models\Integration;
use App\Models\Theme;
use App\Services\Integration\ThemeIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SyncThemeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        private readonly int $themeId,
        private readonly string $action, // 'create' | 'update' | 'delete'
    ) {
        // Не отправлять в API раньше, чем закоммитится транзакция
        $this->afterCommit();
    }

    public function handle(ThemeIntegrationService $service): void
    {
        if (!(bool) Integration::firstOrCreate()->status) {
            return;
        }

        if ($this->action === 'delete') {
            $service->destroy($this->themeId);

            return;
        }

        $theme = Theme::find($this->themeId);

        if (!$theme || !self::inScope($theme)) {
            return;
        }

        match ($this->action) {
            'create' => $service->create($theme),
            'update' => $service->sync($theme),
            default  => throw new \InvalidArgumentException("Unknown action: {$this->action}"),
        };
    }

    /**
     * В приложение передаётся только выставка из EVENTICIOUS_EXHIBITION_ID.
     */
    public static function inScope(Theme $theme): bool
    {
        $exhibitionId = config('services.eventicious.exhibition_id');

        return !$exhibitionId || $theme->exhibition_id === (int) $exhibitionId;
    }
}
