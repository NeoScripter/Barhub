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

class SyncCompanyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;
    public int $backoff = 30;

    // Не отправлять в API раньше, чем закоммитится транзакция со связями
    public bool $afterCommit = true;

    public function __construct(
        private readonly int $companyId,
        private readonly string $action, // 'create' | 'update' | 'delete'
    ) {}

    public function handle(CompanyIntegrationService $service): void
    {
        if (!(bool) Integration::firstOrCreate()->status) {
            return;
        }

        if ($this->action === 'delete') {
            $service->destroy($this->companyId);

            return;
        }

        $company = Company::find($this->companyId);

        if (!$company || !self::inScope($company)) {
            return;
        }

        // Компанию, скрытую с сайта, убираем и из приложения
        if (!$company->show_on_site) {
            $service->destroy($company->id);

            return;
        }

        match ($this->action) {
            'create' => $service->create($company),
            'update' => $service->sync($company),
            default  => throw new \InvalidArgumentException("Unknown action: {$this->action}"),
        };
    }

    /**
     * В приложение передаётся только выставка из EVENTICIOUS_EXHIBITION_ID
     * (ключ Eventicious привязан к одному мероприятию).
     */
    public static function inScope(Company $company): bool
    {
        $exhibitionId = config('services.eventicious.exhibition_id');

        return !$exhibitionId || $company->exhibition_id === (int) $exhibitionId;
    }
}
