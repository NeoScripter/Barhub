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
use Illuminate\Queue\SerializesModels;

class SyncThemeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        private readonly Theme $theme,
        private readonly string $action, // 'create' | 'update' | 'destroy'
    ) {}

    public function handle(ThemeIntegrationService $service): void
    {
        $integration = Integration::firstOrCreate();

        if ((bool) $integration->status === true) {
            match ($this->action) {
                'create' => $service->create($this->theme),
                'update' => $service->update($this->theme),
                'delete' => $service->destroy($this->theme),
                default  => throw new \InvalidArgumentException("Unknown action: {$this->action}"),
            };
        }
    }
}
