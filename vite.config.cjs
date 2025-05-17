const { defineConfig } = require('vite');
const laravel = require('laravel-vite-plugin');

module.exports = defineConfig({
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
});