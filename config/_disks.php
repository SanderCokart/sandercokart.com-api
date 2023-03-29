<?php

use App\Enums\Disk;

return [
    Disk::privateArticles->name => [
        'driver' => 'local',
        'root'   => storage_path('app/private/articles'),
        'url'    => env('APP_URL') . '/storage/articles',
        'throw'  => false,
    ],

    Disk::publishedArticles->name => [
        'driver' => 'local',
        'root'   => storage_path('app/public/articles'),
        'url'    => env('APP_URL') . '/storage/articles',
        'throw'  => false,
    ],
];
