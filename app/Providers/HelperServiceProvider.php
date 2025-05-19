<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\CurrencyHelper;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register a blade directive for formatting currency as ZAR
        Blade::directive('zar', function ($expression) {
            return "<?php echo \App\Helpers\CurrencyHelper::formatZAR($expression); ?>";
        });
    }
}
