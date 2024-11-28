import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  server: {
    proxy: {
      '/api/v1': {
        target: 'http://localhost:4000', // Your Go backend
        changeOrigin: true,
        rewrite: (path) => { 
          console.log(path)
          path.replace(/^\/api\/v1/, '/api/v1')
          console.log(path)
          return path
        }, // Ensures the path is preserved
      },
    },
  },
})