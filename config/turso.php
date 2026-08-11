<?php

return [
    'db' => [
        'url'          => env('DB_DATABASE', env('TURSO_DATABASE_URL')),
        'access_token' => env('DB_AUTH_TOKEN', env('TURSO_AUTH_TOKEN')),
    ],
    'url'          => env('DB_DATABASE', env('TURSO_DATABASE_URL')),
    'access_token' => env('DB_AUTH_TOKEN', env('TURSO_AUTH_TOKEN')),
];