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

class SyncEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;
    public int $backoff = 30;

    // Джоба диспатчится из транзакции, где связи (спикеры, темы) ещё не
    // закоммичены — без afterCommit сессия улетела бы в API пустой.
    public bool $afterCommit = true;

    public function __construct(
        private readonly int $eventId,
        private readonly string $action, // 'create' | 'update' | 'delete'
    ) {}

    public function handle(EventIntegrationService $service): void
    {
        if (!(bool) Integration::firstOrCreate()->status) {
            return;
        }

        if ($this->action === 'delete') {
            $service->destroy($this->eventId);

            return;
        }

        $event = Event::find($this->eventId);

        if (!$event || !self::inScope($event)) {
            return;
        }

        match ($this->action) {
            'create' => $service->create($event),
            'update' => $service->sync($event),
            default  => throw new \InvalidArgumentException("Unknown action: {$this->action}"),
        };
    }

    /**
     * В приложение передаётся только выставка из EVENTICIOUS_EXHIBITION_ID.
     */
    public static function inScope(Event $event): bool
    {
        $exhibitionId = config('services.eventicious.exhibition_id');

        return !$exhibitionId || $event->exhibition_id === (int) $exhibitionId;
    }
}
