<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\SendBackupNotification;
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

    exec("mysqldump -u {$username} --password={$password} {$database} > {$path}");

    $gzdata = gzencode(file_get_contents($path));
    file_put_contents("{$path}.gz", $gzdata);

    if (file_exists($path)) {
        unlink($path);
    }

    $user = User::where('email', 'gena-bar@mail.ru')->first();

    if ($user) {
        $user->notify(new SendBackupNotification("{$path}.gz"));
    }

    $this->comment('db successfully backed up');
})->purpose('Backup mysql database');
