<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\HelperServiceProvider::class,
    App\Providers\AuthServiceProvider::class, // Assuming this should be here too
    App\Providers\BroadcastServiceProvider::class,
    App\Providers\EventServiceProvider::class, // Assuming this should be here too
    App\Providers\RouteServiceProvider::class, // Assuming this should be here too
    App\Providers\ViteServiceProvider::class, // Our custom Vite service provider
];
