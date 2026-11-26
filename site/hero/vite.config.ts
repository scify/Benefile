import react from '@vitejs/plugin-react'
import { defineConfig } from 'vite'

// One script and one stylesheet with fixed names, so the pages link them
// directly. The mark of BeneFile is small enough to go inside the script.
export default defineConfig({
  plugins: [react()],
  build: {
    outDir: '../assets/hero',
    emptyOutDir: true,
    assetsInlineLimit: 8192,
    cssCodeSplit: false,
    rollupOptions: {
      input: 'src/main.tsx',
      output: {
        format: 'es',
        entryFileNames: 'hero.js',
        assetFileNames: 'hero[extname]',
      },
    },
  },
})
