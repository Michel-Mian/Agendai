import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/explore.css',
                'resources/css/nightMode.css',
                'resources/js/app.js',
                'resources/js/dashBoard.js',
                'resources/js/searchFlights.js',
                'resources/js/formTrip.js',
                'resources/js/hotels.js',
                'resources/js/nightMode.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        hmr: {
            host: 'localhost',
        },
    },
});
