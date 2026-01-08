import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Base CSS/JS
                'resources/css/app.css',
                'resources/js/app.js',
                // Widget CSS
                'resources/css/widgets/hero.css',
                'resources/css/widgets/gallery.css',
                'resources/css/widgets/map.css',
                // Widget JS (only where needed)
                'resources/js/widgets/map.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                // Per-widget chunks
                chunkFileNames: 'widgets/[name]-[hash].js',
                entryFileNames: 'widgets/[name]-[hash].js',
                assetFileNames: 'widgets/[name]-[hash].[ext]',
            },
        },
    },
});
