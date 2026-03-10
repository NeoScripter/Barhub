<?php

declare(strict_types=1);

test('all pages open and have no javascript errors', function (): void {

    $routes = [
        '/',
        '/admin/dashboard',
        '/admin/exhibitions',
        '/admin/exhibitions/{exhibition}',
        '/admin/exhibitions/{exhibition}/all-tasks',
        '/admin/exhibitions/{exhibition}/all-tasks/{all_task}/edit',
        '/admin/exhibitions/{exhibition}/companies',
        '/admin/exhibitions/{exhibition}/companies/create',
        '/admin/exhibitions/{exhibition}/companies/{company}/edit',
        '/admin/exhibitions/{exhibition}/companies/{company}/exponents',
        '/admin/exhibitions/{exhibition}/companies/{company}/services',
        '/admin/exhibitions/{exhibition}/companies/{company}/services/create',
        '/admin/exhibitions/{exhibition}/companies/{company}/services/{service}',
        '/admin/exhibitions/{exhibition}/companies/{company}/services/{service}/edit',
        '/admin/exhibitions/{exhibition}/companies/{company}/tasks',
        '/admin/exhibitions/{exhibition}/companies/{company}/tasks/create',
        '/admin/exhibitions/{exhibition}/companies/{company}/tasks/{task}',
        '/admin/exhibitions/{exhibition}/companies/{company}/tasks/{task}/edit',
        '/admin/exhibitions/{exhibition}/edit',
        '/admin/exhibitions/{exhibition}/events',
        '/admin/exhibitions/{exhibition}/events/create',
        '/admin/exhibitions/{exhibition}/events/{event}',
        '/admin/exhibitions/{exhibition}/events/{event}/edit',
        '/admin/exhibitions/{exhibition}/followups',
        '/admin/exhibitions/{exhibition}/followups/{followup}/edit',
        '/admin/exhibitions/{exhibition}/people',
        '/admin/exhibitions/{exhibition}/people/create',
        '/admin/exhibitions/{exhibition}/people/{person}',
        '/admin/exhibitions/{exhibition}/people/{person}/edit',
        '/email/verify',
        '/email/verify/{id}/{hash}',
        '/exhibitions',
        '/exhibitions/{exhibition}/events',
        '/exhibitions/{exhibition}/events/{event}',
        '/exponent/dashboard',
        '/forgot-password',
        '/login',
        '/register',
        '/reset-password/{token}',
        '/storage/{path}',
        '/up',
        '/user/confirm-password',
        '/user/confirmed-password-status',
    ];

    visit($routes)->assertNoSmoke();
});
