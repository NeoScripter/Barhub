<?php

declare(strict_types=1);

namespace App\Jobs\Integration;

use App\Models\Integration;
use App\Models\Person;
use App\Services\Integration\PersonIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncPersonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        private readonly Person $person,
        private readonly string $action, // 'create' | 'update' | 'destroy'
    ) {}

    public function handle(PersonIntegrationService $service): void
    {
        $integration = Integration::firstOrCreate();

        if ((bool) $integration->status === true) {
            match ($this->action) {
                'create' => $service->create($this->person),
                'update' => $service->update($this->person),
                'delete' => $service->destroy($this->person),
                default  => throw new \InvalidArgumentException("Unknown action: {$this->action}"),
            };
        }
    }
}
