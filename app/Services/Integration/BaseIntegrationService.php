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
    private const BASE_URL        = 'https://api-integration.eventicious.ru';

    protected function get(string $endpoint): Response
    {
        return $this->send('get', $endpoint);
    }

    protected function post(string $endpoint, array $data): Response
    {
        return $this->send('post', $endpoint, $data);
    }

    protected function put(string $endpoint, array $data): Response
    {
        return $this->send('put', $endpoint, $data);
    }

    protected function patch(string $endpoint, array $data): Response
    {
        return $this->send('patch', $endpoint, $data);
    }

    protected function delete(string $endpoint, ?array $data = []): Response
    {
        return $this->send('delete', $endpoint, $data);
    }

    /**
     * Отправка запроса с одним повтором при 401: токен могли отозвать
     * досрочно — сбрасываем кэш, берём новый и повторяем запрос.
     */
    private function send(string $method, string $endpoint, ?array $data = null): Response
    {
        $request = fn (): Response => Http::withToken($this->token())
            ->timeout(30)
            ->{$method}(self::BASE_URL . $endpoint, $data);

        $response = $request();

        if ($response->status() === 401) {
            Cache::forget(self::TOKEN_CACHE_KEY);
            $response = $request();
        }

        return $response;
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
        $cached = Cache::get(self::TOKEN_CACHE_KEY);

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

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

        // Токен живёт expires_in секунд; кэшируем с минутным запасом.
        $ttl = max(60, (int) $response->json('expires_in', 3600) - 60);
        Cache::put(self::TOKEN_CACHE_KEY, $token, $ttl);

        return $token;
    }
}
