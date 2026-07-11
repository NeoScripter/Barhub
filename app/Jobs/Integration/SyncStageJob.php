<?php

declare(strict_types=1);

namespace App\Jobs\Integration;

use App\Models\Integration;
use App\Models\Stage;
use App\Services\Integration\StageIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SyncStageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        private readonly int $stageId,
        private readonly string $action, // 'create' | 'update' | 'delete'
    ) {
        // Не отправлять в API раньше, чем закоммитится транзакция
        $this->afterCommit();
    }

    public function handle(StageIntegrationService $service): void
    {
        if (!(bool) Integration::firstOrCreate()->status) {
            return;
        }

        if ($this->action === 'delete') {
            $service->destroy($this->stageId);

            return;
        }

        $stage = Stage::find($this->stageId);

        if (!$stage || !self::inScope($stage)) {
            return;
        }

        match ($this->action) {
            'create' => $service->create($stage) || $service->update($stage),
            'update' => $service->sync($stage),
            default  => throw new \InvalidArgumentException("Unknown action: {$this->action}"),
        };
    }

    /**
     * В приложение передаётся только выставка из EVENTICIOUS_EXHIBITION_ID.
     */
    public static function inScope(Stage $stage): bool
    {
        $exhibitionId = config('services.eventicious.exhibition_id');

        return !$exhibitionId || $stage->exhibition_id === (int) $exhibitionId;
    }
}
