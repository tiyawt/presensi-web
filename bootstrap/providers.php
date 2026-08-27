<?php

$providers = [
    App\Providers\AppServiceProvider::class,
];

if (env('DB_CONNECTION') === 'libsql') {
    $providers[] = \Turso\Http\Laravel\LibSQLHttpServiceProvider::class;
}

return $providers;