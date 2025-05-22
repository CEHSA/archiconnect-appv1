<?php

namespace App\Helpers;

class ViteHelper
{
    /**
     * Check if the application is in production mode
     *
     * @return bool
     */
    public static function isProduction(): bool
    {
        return app()->environment('production');
    }

    /**
     * Check if Vite assets have been built
     *
     * @return bool
     */
    public static function assetsBuilt(): bool
    {
        return file_exists(public_path('build/manifest.json'));
    }

    /**
     * Get Vite tags for the given assets
     *
     * @param array $assets
     * @return string
     */
    public static function tags(array $assets): string
    {
        // Production environment - use the Vite directive
        if (self::isProduction()) {
            // In production, we should always have built assets
            return '@vite(' . json_encode($assets) . ')';
        }

        // Development environment with built assets
        if (self::assetsBuilt()) {
            return '@vite(' . json_encode($assets) . ')';
        }

        // Development environment without built assets - fallback to Vite dev server
        $html = [];
        $html[] = '<script type="module" src="http://localhost:5173/@vite/client"></script>';
        
        foreach ($assets as $asset) {
            if (str_ends_with($asset, '.css')) {
                $html[] = '<link rel="stylesheet" href="http://localhost:5173/' . $asset . '" />';
            } elseif (str_ends_with($asset, '.js')) {
                $html[] = '<script type="module" src="http://localhost:5173/' . $asset . '"></script>';
            }
        }

        return implode("\n        ", $html);
    }
}
