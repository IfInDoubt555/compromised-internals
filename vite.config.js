import path from 'node:path';
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
       resolve: {
     alias: {
       // FullCalendar CSS subpaths (map to real files to bypass package "exports")
       '@fullcalendar/daygrid/index.css': path.resolve(__dirname, 'node_modules/@fullcalendar/daygrid/index.css'),
       // (optional future-proofers—safe to include)
       '@fullcalendar/daygrid/main.css':  path.resolve(__dirname, 'node_modules/@fullcalendar/daygrid/main.css'),
       '@fullcalendar/core/index.css':    path.resolve(__dirname, 'node_modules/@fullcalendar/core/index.css'),
       '@fullcalendar/core/main.css':     path.resolve(__dirname, 'node_modules/@fullcalendar/core/main.css'),
     },
   },
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