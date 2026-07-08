<?php

declare(strict_types=1);

namespace App\Services\Integration;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

abstract class BaseIntegrationService
{
    private const TOKEN_CACHE_KEY = 'eventicious_api_token';
    private const TOKEN_TTL       = 60;
    private const BASE_URL        = 'https://api-integration.eventicious.ru';

    protected function get(string $endpoint): Response
    {
        return Http::withToken($this->token())
            ->get(self::BASE_URL . $endpoint);
    }

    protected function post(string $endpoint, array $data): Response
    {
        return Http::withToken($this->token())
            ->post(self::BASE_URL . $endpoint, $data);
    }

    protected function put(string $endpoint, array $data): Response
    {
        return Http::withToken($this->token())
            ->put(self::BASE_URL . $endpoint, $data);
    }

    protected function delete(string $endpoint, ?array $data = []): Response
    {
        return Http::withToken($this->token())
            ->delete(self::BASE_URL . $endpoint, $data);
    }

    protected function parse_error(Response $response): string
    {
        $body = $response->json();

        return $body['title'] ?? "HTTP {$response->status()} from {$response->effectiveUri()}";
    }

    protected function log_error(string $message, array $context = []): void
    {
        Log::channel('integration')->error("[Eventicious] {$message}", $context);
    }

    protected function log_info(string $message, array $context = []): void
    {
        Log::channel('integration')->info("[Eventicious] {$message}", $context);
    }

    private function token(): string
    {
        return Cache::remember(self::TOKEN_CACHE_KEY, self::TOKEN_TTL, function () {
            $response = Http::asForm()->post(self::BASE_URL . '/connect/token', [
                'grant_type'    => 'client_credentials',
                'client_id'     => config('services.eventicious.client_id'),
                'client_secret' => config('services.eventicious.client_secret'),
            ]);

            if (!$response->successful()) {
                throw new RuntimeException('Eventicious: failed to obtain access token. Status: ' . $response->status());
            }

            $token = $response->json('access_token');

            if (!$token) {
                throw new RuntimeException('Eventicious: access_token missing from token response.');
            }

            return $token;
        });
    }
}
