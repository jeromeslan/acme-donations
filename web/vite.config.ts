import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'node:path'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: { '@': path.resolve(__dirname, 'src') },
  },
  server: {
    host: true,
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://api-nginx:80',
        changeOrigin: true,
        secure: false,
      },
      '/sanctum': {
        target: 'http://api-nginx:80',
        changeOrigin: true,
        secure: false,
      },
    },
  },
})


