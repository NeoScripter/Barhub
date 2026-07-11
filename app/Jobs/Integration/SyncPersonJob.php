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

class SyncPersonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        private readonly int $personId,
        private readonly string $action, // 'create' | 'update' | 'delete'
    ) {
        // Не отправлять в API раньше, чем закоммитится транзакция со связями
        $this->afterCommit();
    }

    public function handle(PersonIntegrationService $service): void
    {
        if (!(bool) Integration::firstOrCreate()->status) {
            return;
        }

        if ($this->action === 'delete') {
            $service->destroy($this->personId);

            return;
        }

        $person = Person::find($this->personId);

        if (!$person || !self::inScope($person)) {
            return;
        }

        match ($this->action) {
            'create' => $service->create($person) || $service->update($person),
            'update' => $service->sync($person),
            default  => throw new \InvalidArgumentException("Unknown action: {$this->action}"),
        };
    }

    /**
     * В приложение передаётся только выставка из EVENTICIOUS_EXHIBITION_ID.
     */
    public static function inScope(Person $person): bool
    {
        $exhibitionId = config('services.eventicious.exhibition_id');

        return !$exhibitionId
            || $person->exhibitions()->whereKey((int) $exhibitionId)->exists();
    }
}
