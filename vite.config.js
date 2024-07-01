import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
        ]),
        // vue({
        //     template: {
        //         transformAssetUrls: {
        //             base: null,
        //             includeAbsolute: false,
        //         },
        //     },
        // }),
    ],
    server: {
        fs: {
            // Allow serving files from one level up to the project root
            allow: ['..'],
        },
        origin: 'https://jobtron.org/',
        hmr: {
            host: 'jobtron.org',
        },
    },
});