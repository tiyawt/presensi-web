<?php

$providers = [
    App\Providers\AppServiceProvider::class,
];

if (env('DB_CONNECTION') === 'libsql' && class_exists('DarkTerminal\TursoHttp\TursoHttpServiceProvider')) {
    $providers[] = DarkTerminal\TursoHttp\TursoHttpServiceProvider::class;
}

return $providers;