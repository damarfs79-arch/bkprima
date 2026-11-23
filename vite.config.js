import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'

export default defineConfig({
  plugins: [
    {
      name: 'redirect-root-to-app',
      configureServer(server) {
        server.middlewares.use((req, res, next) => {
          if (req.url === '/' || req.url === '') {
            res.writeHead(302, { Location: 'http://bkprima.test' })
            res.end()
            return
          }
          next()
        })
      }
    },
    laravel({
      input: ['resources/js/app.js'],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
      'vue-router': fileURLToPath(new URL('./resources/js/inertia-compat.js', import.meta.url))
    }
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    cors: true,
    hmr: {
      host: 'localhost',
    },
  }
})
