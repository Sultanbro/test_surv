import { defineConfig, type UserConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { visualizer } from 'rollup-plugin-visualizer';
import vue from '@vitejs/plugin-vue2';
import checker from 'vite-plugin-checker';
import path from 'path';
import fs from 'fs';
import { VuetifyResolver } from 'unplugin-vue-components/resolvers';
import Components from 'unplugin-vue-components/vite';

// https://vitejs.dev/config/
export default defineConfig(async ({ mode }): Promise<UserConfig> => {
  const config: UserConfig = {
    // https://vitejs.dev/config/#server-options
    server: {
      fs: {
        // Allow serving files from one level up to the project root
        allow: ['..'],
      },
      host: 'chat.rostkov.me',
      origin: 'http://chat.rostkov.me/',
      hmr: {
        host: 'chat.rostkov.me',
      },
    },
    plugins: [
      // Laravel Vite
      // https://laravel.com/docs/9.x/vite
      laravel({
        input: ['resources/js/app.ts'],
        refresh: true,
      }),
      // Vue2
      // https://github.com/vitejs/vite-plugin-vue2
      vue({
        template: {
          transformAssetUrls: {
            // The Vue plugin will re-write asset URLs, when referenced
            // in Single File components, to point to the Laravel web
            // server. Setting this to `null` allows the Laravel plugin
            // to instead re-write asset URLs to point to the Vite
            // server instead.
            base: null,

            // The Vue plugin will parse absolute URLs and treat them
            // as absolute paths to files on disk. Setting this to
            // `false` will leave absolute URLs un-touched so they can
            // reference assets in the public directly as expected.
            // includeAbsolute: false,
          },
        },
      }),
      // unplugin-vue-components
      // https://github.com/antfu/unplugin-vue-components
      Components({
        // generate `components.d.ts` global declarations
        // https://github.com/antfu/unplugin-vue-components#typescript
        dts: true,
        // auto import for directives
        directives: false,
        // resolvers for custom components
        resolvers: [
          // Vuetify
          VuetifyResolver(),
        ],
        // https://github.com/antfu/unplugin-vue-components#types-for-global-registered-components
        types: [
          {
            from: 'vue-router',
            names: ['RouterLink', 'RouterView'],
          },
        ],
      }),
      // vite-plugin-checker
      // https://github.com/fi3ework/vite-plugin-checker
      checker({
        typescript: true,
        vueTsc: false,
        eslint: {
          lintCommand: 'eslint', // for example, lint .ts & .tsx
        },
      }),
    ],
    // Build Options
    // https://vitejs.dev/config/#build-options
    build: {
      rollupOptions: {
        output: {
          manualChunks: {
            // Split external library from transpiled code.
            vue: [
              'vue',
              'vue-class-component',
              'vue-property-decorator',
              'vue-router',
              // 'vuex',
              // 'vuex-persist',
              'vue2-teleport',
              'deepmerge',
              '@logue/vue2-helpers',
              '@logue/vue2-helpers/vue-router',
              // '@logue/vue2-helpers/vuex',
              // 'vue-style-loader',
              // 'css-loader',
            ],
            vuetify: [
              'vuetify/lib',
              '@logue/vue2-helpers/vuetify',
              'webfontloader',
            ],
            materialdesignicons: ['@mdi/font/css/materialdesignicons.css'],
            inertia: [
              '@inertiajs/inertia-vue/dist/index.js',
              '@inertiajs/inertia',
              '@inertiajs/progress',
              'get-intrinsic',
              'laravel-vite-plugin/inertia-helpers/index.js',
              'nprogress',
              'object-inspect',
              'qs',
              'vendor/tightenco/ziggy/dist/vue.m.js',
              'vue-inertia-composable',
              'ziggy-js',
            ],
            axios: ['axios'],
            lodash: ['lodash'],
          },
          plugins: [
            mode === 'analyze'
              ? // rollup-plugin-visualizer
                // https://github.com/btd/rollup-plugin-visualizer
              visualizer({
                open: true,
                filename: './stats.html',
                gzipSize: true,
                brotliSize: true,
              })
              : undefined,
            /*
            // if you use Code encryption by rollup-plugin-obfuscator
            // https://github.com/getkey/rollup-plugin-obfuscator
            obfuscator({
              globalOptions: {
                debugProtection: true,
              },
            }),
            */
          ],
        },
      },
      target: 'es2021',
      /*
      // Minify option
      // https://vitejs.dev/config/#build-minify
      minify: 'terser',
      terserOptions: {
        ecma: 2020,
        parse: {},
        compress: { drop_console: true },
        mangle: true, // Note `mangle.properties` is `false` by default.
        module: true,
        output: { comments: true, beautify: false },
      },
      */
    },
  };

  // Write meta data.
  fs.writeFileSync(
    path.resolve(path.join(__dirname, 'resources/js/meta.ts')),
    `// This file is auto-generated by the build system.
export default {
  date: '${new Date().toISOString()}',
};`
  );

  return config;
});
