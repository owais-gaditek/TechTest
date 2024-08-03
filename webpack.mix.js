const mix = require('laravel-mix');
const path = require('path');

mix.setPublicPath('public')
   .js('resources/js/article.js', 'js')
   .js('resources/js/fibonacci.js', 'js')
   .sass('resources/sass/app.scss', 'css')
   .options({
       processCssUrls: false
   })
   .sourceMaps();

mix.webpackConfig({
    resolve: {
        alias: {
            '@': path.resolve('resources/js')
        },
        extensions: ['.js', '.jsx', '.css', '.scss']
    }
});
