<?php

declare(strict_types=1);

namespace App\Jobs\Integration;

use App\Models\Event;
use App\Models\Integration;
use App\Services\Integration\EventIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        private readonly Event $event,
        private readonly string $action, // 'create' | 'update' | 'destroy'
    ) {}

    public function handle(EventIntegrationService $service): void
    {
        $integration = Integration::firstOrCreate();

        if ((bool) $integration->status === true) {
            match ($this->action) {
                'create' => $service->create($this->event),
                'update' => $service->update($this->event),
                'delete' => $service->destroy($this->event),
                default  => throw new \InvalidArgumentException("Unknown action: {$this->action}"),
            };
        } else {

            Log::channel('integration')->error("[Eventicious] Integration is disabled");
        }
    }
}
