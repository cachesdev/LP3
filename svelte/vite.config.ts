import { sveltekit } from "@sveltejs/kit/vite";
import { defineConfig } from "vite";

export default defineConfig({
	plugins: [sveltekit()],
	server: {
		proxy: {
			"/api/v1": {
				target: "http://localhost:4000",
				changeOrigin: true,
				rewrite: (path) => {
					path.replace(/^\/api\/v1/, "/api/v1");
					return path;
				},
			},
		},
	},
});
