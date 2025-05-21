import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ command }) => {
    const cfg = {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/css/fade.css',
                    'resources/js/app.js',
                    'resources/js/history.js',
                ],
                refresh: true,
            }),
        ],
    };

    if (command === 'serve') {
        cfg.server = {
            // listen on all network interfaces (so .test resolves)
            host: true,
            // your local site URL
            origin: 'http://compromised-internals.test:5173',
            hmr: {
                // tell the client to connect here for HMR
                host: 'compromised-internals.test',
                protocol: 'ws',
                port: 5173,
            },
        };
    }

    return cfg;
});
