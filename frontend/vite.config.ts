/**
 * Iran-Daily — Vite Configuration
 *
 * @author THE SAZ <https://github.com/THE-SAZ>
 */

import { defineConfig } from 'vite';

export default defineConfig({
  server: {
    port: 3000,
    proxy: {
      '/api': {
        target: 'http://localhost:8080',
        changeOrigin: true,
      },
    },
  },
  build: {
    outDir: 'dist',
    sourcemap: false,
    minify: 'esbuild',
    target: 'es2022',
    rollupOptions: {
      output: {
        manualChunks: undefined,
      },
    },
  },
});
