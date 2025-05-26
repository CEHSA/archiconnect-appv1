import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Generate manifest for production
        manifest: true,
        // Optimize build
        minify: 'terser',
        rollupOptions: {
            output: {
                entryFileNames: '[name].js',
                chunkFileNames: '[name].js',
                assetFileNames: '[name].[ext]',
                manualChunks: {
                    vendor: [
                        // List vendor modules here if needed
                    ]
                }
            }
        }
    },
    // Ensure proper asset paths in production
    base: '/build/',
});
