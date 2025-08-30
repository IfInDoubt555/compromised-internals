import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

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
  }

  if (command === 'serve') {
    cfg.server = {
      host: true,                                // allow .test host header
      cors: true,                                // <-- fix CORS
      origin: 'http://compromised-internals.test:5173',
      strictPort: true,
      hmr: {
        host: 'compromised-internals.test',      // websocket host for HMR
        protocol: 'ws',                          // ws for http (wss if https)
        port: 5173,
      },
    }
  }

  return cfg
})