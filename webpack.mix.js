/* global __dirname */

const path = require('path')
const fs = require('fs')
const mix = require('laravel-mix')
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
	.sass('resources/sass/admin.scss', 'public/css/admin/app.css')
	.options({
		terser: {
			extractComments: false,
		}
	})
	.then(() => {
		// незнаю как в mix добавить хэш к css без использования тормозного mix.version() поэтому такой вот костыль
		const now = Date.now()
		const mainfest = require('./public/mix-manifest.json')
		Object.keys(mainfest).forEach(key => {
			if(key.indexOf('.css') !== -1){
				mainfest[key] = key + '?id=' + now
			}
		})
		fs.writeFileSync('./public/mix-manifest.json', JSON.stringify(mainfest, null, 4))
	})

mix.webpackConfig({
	resolve: {
		fallback: { timers: require.resolve('timers-browserify') },
		alias: {
			'@': path.resolve(__dirname, 'resources/js'),
			'@ui': path.resolve(__dirname, 'resources/js/components/ui'),
			'@icons': path.resolve(__dirname, 'resources/js/components/ui/Icons'),
		},
	},
	output: {
		filename: '[name].js?id=[hash]',
		chunkFilename: '[name].js?id=[hash]'
	}
});

mix.js('resources/js/home.js', 'public/js').vue();

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

