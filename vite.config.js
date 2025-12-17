import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        host: true,
        hmr: false,
    },

    build: {
        manifest: true,
        outDir: 'public/build',
        emptyOutDir: true,
    },

    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: false,
        }),
        tailwindcss(),
    ],
});
