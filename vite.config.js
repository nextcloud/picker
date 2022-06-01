import { resolve } from 'path'
import { defineConfig } from 'vite'
import { createVuePlugin } from 'vite-plugin-vue2'
import commonjs from 'vite-plugin-commonjs'

export default defineConfig({
	plugins: [createVuePlugin(), commonjs],
	build: {
		// manifest: true,
		polyfillModulePreload: true,
		outDir: './js',
		rollupOptions: {
			input: {
				main: resolve(__dirname, 'src/main.js'),
			},
			output: {
				// manualChunks: false,
				// inlineDynamicImports: true,
				entryFileNames: 'picker-[name].js',
				chunkFileNames: `picker-[name].js`,
				assetFileNames: 'picker-[name].[ext]',
				// globals: { vue: 'Vue' },
			},
		},
		minify: false,
	},
})
