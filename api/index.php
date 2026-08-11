<?php

use Illuminate\Http\Request;

// 1. Buat folder sementara di /tmp (writable di serverless Vercel)
$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/bootstrap/cache',
    '/tmp/storage/logs',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 2. Set environment variable storage
$_ENV['APP_STORAGE_PATH'] = '/tmp/storage';

// 3. Load Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 4. Bootstrap Application (Laravel 11 Way)
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 5. Pindahkan path Storage & Bootstrap Cache ke /tmp
$app->useStoragePath('/tmp/storage');
$app->useBootstrapPath('/tmp/storage/bootstrap');

// 6. Jalankan Aplikasi (Cara Ringkas Laravel 11)
$app->handleRequest(Request::capture());