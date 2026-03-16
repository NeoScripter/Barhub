<?php

declare(strict_types=1);

test('all pages open and have no javascript errors', function (): void {

    $routes = [
        "/",
        "/admin/all-tasks",
        "/admin/all-tasks/{all_task}/edit",
        "/admin/companies",
        "/admin/companies/create",
        "/admin/companies/{company}/edit",
        "/admin/companies/{company}/exponents",
        "/admin/companies/{company}/services",
        "/admin/companies/{company}/services/create",
        "/admin/companies/{company}/services/{service}",
        "/admin/companies/{company}/services/{service}/edit",
        "/admin/companies/{company}/tasks",
        "/admin/companies/{company}/tasks/create",
        "/admin/companies/{company}/tasks/{task}",
        "/admin/companies/{company}/tasks/{task}/edit",
        "/admin/dashboard",
        "/admin/events",
        "/admin/events/create",
        "/admin/events/{event}/edit",
        "/admin/exhibitions",
        "/admin/exhibitions/create",
        "/admin/exhibitions/{exhibition}",
        "/admin/exhibitions/{exhibition}//admins",
        "/admin/exhibitions/{exhibition}/edit",
        "/admin/followups",
        "/admin/followups/{followup}/edit",
        "/admin/info-items",
        "/admin/info-items/create",
        "/admin/info-items/{info_item}",
        "/admin/info-items/{info_item}/edit",
        "/admin/links",
        "/admin/people",
        "/admin/people/create",
        "/admin/people/{person}/edit",
        "/admin/task-templates",
        "/admin/task-templates/create",
        "/admin/task-templates/{task_template}",
        "/admin/task-templates/{task_template}/edit",
        "/email/verify",
        "/email/verify/{id}/{hash}",
        "/exhibitions",
        "/exhibitions/{exhibition}/events",
        "/exhibitions/{exhibition}/events/{event}",
        "/exponent/dashboard",
        "/forgot-password",
        "/login",
        "/register",
        "/reset-password/{token}",
        "/user/confirm-password",
        "/user/confirmed-password-status"
    ];

    visit($routes)->assertNoSmoke();
});
