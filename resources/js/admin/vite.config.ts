import { fileURLToPath } from 'url'
import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import DefineOptions from 'unplugin-vue-define-options/vite'
import { defineConfig } from 'vite'
import Pages from 'vite-plugin-pages'
import Layouts from 'vite-plugin-vue-layouts'
import vuetify from 'vite-plugin-vuetify'

// https://vitejs.dev/config/
export default defineConfig({
  appType: 'custom',
  plugins: [
    vue(),
    vueJsx(),

    // https://github.com/vuetifyjs/vuetify-loader/tree/next/packages/vite-plugin
    vuetify({
      styles: {
        configFile: 'src/styles/variables/_vuetify.scss',
      },
    }),
    Pages({
      extendRoute(route) {
        if (route.path === '/admino/login') {
          return {
            ...route,
            meta: {
              layout: 'blank',
            },
          }
        }

        return {
          ...route,
          meta: {
            auth: true,
          },
        }
      },
    }),
    Layouts(),
    Components({
      dirs: ['src/@core/components'],
      dts: true,
    }),
    AutoImport({
      imports: ['vue', 'vue-router', '@vueuse/core', 'vue-i18n', 'pinia'],
      vueTemplate: true,
    }),
    DefineOptions(),
  ],
  define: { 'process.env': {} },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
      '@core': fileURLToPath(new URL('./src/@core', import.meta.url)),
      '@layouts': fileURLToPath(new URL('./src/@layouts', import.meta.url)),
      '@configured-variables': fileURLToPath(new URL('./src/styles/variables/_template.scss', import.meta.url)),
      '@axios': fileURLToPath(new URL('./src/plugins/axios', import.meta.url)),
      'apexcharts': fileURLToPath(new URL('node_modules/apexcharts-clevision', import.meta.url)),
    },
  },
  build: {
    chunkSizeWarningLimit: 5000,
    outDir: '../../../public/js/admino',
    rollupOptions: {
      output: {
        entryFileNames: 'assets/[name].js',
        chunkFileNames: 'assets/[name].js',
        assetFileNames: 'assets/[name].[ext]',
      },
    },
  },
  optimizeDeps: {
    exclude: ['vuetify'],
    entries: [
      './src/**/*.vue',
    ],
  },
  experimental: {
    renderBuiltUrl(filename: string) {
      return `/js/admino/${filename}`
    },
  },
})
