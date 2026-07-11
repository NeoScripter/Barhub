<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'eventicious' => [
        'client_id'     => env('EVENTICIOUS_CLIENT_ID'),
        'client_secret' => env('EVENTICIOUS_CLIENT_SECRET'),
        // ID выставки на сайте, данные которой передаются в Eventicious
        // (ключ API привязан к одному мероприятию). Пусто = без фильтра.
        'exhibition_id' => env('EVENTICIOUS_EXHIBITION_ID'),
        // Часовой пояс мероприятия: БД хранит UTC, Eventicious ждёт локальное время
        'timezone'      => env('EVENTICIOUS_TIMEZONE', 'Europe/Moscow'),
    ],

];
