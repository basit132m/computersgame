import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        target: ['es2020', 'edge88', 'firefox78', 'chrome87', 'safari14'],
        cssMinify: true,
        rollupOptions: {
            output: {
                manualChunks: {
                    alpine: ['alpinejs'],
                },
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
