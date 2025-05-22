<?php

namespace App\Providers;

use App\Helpers\ViteHelper;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViteServiceProvider extends ServiceProvider
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
        // Register a directive for robust Vite asset handling
        Blade::directive('viteAssets', function ($expression) {
            return "<?php echo app('App\\Helpers\\ViteHelper')->tags($expression); ?>";
        });
    }
}
