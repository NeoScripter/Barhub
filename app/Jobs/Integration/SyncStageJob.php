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
use Illuminate\Queue\SerializesModels;

class SyncStageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        private readonly Stage $stage,
        private readonly string $action, // 'create' | 'update' | 'destroy'
    ) {}

    public function handle(StageIntegrationService $service): void
    {
        $integration = Integration::firstOrCreate();

        if ((bool) $integration->status === true) {
            match ($this->action) {
                'create' => $service->create($this->stage),
                'update' => $service->update($this->stage),
                'delete' => $service->destroy($this->stage),
                default  => throw new \InvalidArgumentException("Unknown action: {$this->action}"),
            };
        }
    }
}
