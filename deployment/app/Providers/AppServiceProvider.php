<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Number;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\View;
use App\Http\View\Composers\AdminLayoutComposer; // Corrected name

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set ZAR as the default currency for the application
        Number::useCurrency('ZAR');

        Relation::morphMap([
            'user' => User::class,
            'admin' => Admin::class,
        ]);

        // Attach the composer to the admin layout component
        View::composer(
            'components.admin-layout', // Target only the admin layout component
            AdminLayoutComposer::class // Corrected composer name
        );
    }
}
