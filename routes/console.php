<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('backup_database', function (): void {
    $username = config('database.connections.mysql.username');
    $database = config('database.connections.mysql.database');
    $password = config('database.connections.mysql.password');
    $path = storage_path('app/private/backup.sql');

    exec( "mysqldump -u {$username} --password={$password} {$database} > {$path}");
    $this->comment('db successfully backed up');
})->purpose('Backup mysql database');

