<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;

class ModelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(User::class, function () {
            return new User();
        });
    }

    public function boot()
    {
        //
    }
}
