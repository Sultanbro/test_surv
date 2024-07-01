mix
  .options({
    terser: {
      terserOptions: {
        compress: {
          drop_console: true,
        },
      },
    },
  })
  .setPublicPath('public')
  .js('resources/js/app.js', 'public')
  .version()
  .webpackConfig({
    resolve: {
      symlinks: false,
      alias: {
        '@': path.resolve(__dirname, 'resources/js/'),
      },
    },
    plugins: [new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)],
  });
