const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */


mix.js('resources/js/app.js', 'public/js').vue()
    .sass('resources/sass/admin.scss', 'public/css/admin/app.css');
    //.webpackConfig(require('./webpack.config'));

mix.webpackConfig({
    resolve: {
        fallback: { "timers": require.resolve('timers-browserify') }
    }
});

// mix.js('resources/assets/js/app.js', 'public/js')
//     // .extract([
//     //     'vue',
//     //     //'jquery',
//     //     'bootstrap-vue',
//     //     'moment',
//     //     'select2',
//     //     'vgauge',
//     //     'axios',
//     //     'v-mask',
//     //     'vue-loading-overlay',
//     //     'vuedraggable',
//     //     'vue-multiselect',
//     //     'vue-select',
//     //     'collect.js',
//     //     'lodash',
//     //     'bootstrap-sass',
//     // ])
//     .sass('resources/assets/sass/admin.scss', 'public/css/admin/app.css')
//     //.sass('public/static/new/css/style.scss', 'public/static/new/css')
//     .options({
//         processCssUrls: false
//     });

// mix.js('resources/js/app.js', 'public/js')
//     .sass('resources/sass/admin.scss', 'public/css/admin/app.css');

