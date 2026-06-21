<?php

declare(strict_types=1);

namespace App\Jobs\Integration;

use App\Models\Company;
use App\Models\Integration;
use App\Services\Integration\CompanyIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncCompanyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        private readonly Company $company,
        private readonly string $action, // 'create' | 'update' | 'destroy'
    ) {}

    public function handle(CompanyIntegrationService $service): void
    {
        $integration = Integration::firstOrCreate();

        if ((bool) $integration->status === true) {
            match ($this->action) {
                'create' => $service->create($this->company),
                'update' => $service->update($this->company),
                'delete' => $service->destroy($this->company),
                default  => throw new \InvalidArgumentException("Unknown action: {$this->action}"),
            };
        }
    }
}
