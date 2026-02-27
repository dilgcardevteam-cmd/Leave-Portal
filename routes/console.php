<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('db:ensure', function () {
    $host = Config::get('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
    $port = Config::get('database.connections.mysql.port', env('DB_PORT', '3306'));
    $database = env('DB_DATABASE', 'leave_portal');
    $username = env('DB_USERNAME', 'root');
    $password = env('DB_PASSWORD', '');

    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $this->info("Database '{$database}' is ready.");
    } catch (Throwable $e) {
        $this->error('Failed to create or access the database: '.$e->getMessage());
    }
})->purpose('Ensure the configured MySQL database exists');
