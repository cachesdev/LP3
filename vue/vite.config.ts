import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
	plugins: [vue(), vueDevTools()],
	resolve: {
		alias: {
			'@': fileURLToPath(new URL('./src', import.meta.url)),
		},
	},
	base: '/vue/',
	server: {
		proxy: {
			'/api/v1': {
				target: 'http://localhost:4000',
				changeOrigin: true,
				rewrite: (path) => {
					path.replace(/^\/api\/v1/, '/api/v1')
					return path
				},
			},
		},
	},
})
