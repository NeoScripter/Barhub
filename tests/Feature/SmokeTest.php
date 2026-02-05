<?php

declare(strict_types=1);

test('all pages open and have no javascript errors', function () {

    $routes = [
        '/',
        '/admin/dashboard',
        '/admin/exhibitions',
        '/email/verify',
        '/forgot-password',
        '/login',
        '/register',
        '/reset-password/{token}',
        '/storage/{path}',
        '/user/confirm-password',
        '/user/confirmed-password-status',
    ];

    visit($routes)->assertNoSmoke();
});
