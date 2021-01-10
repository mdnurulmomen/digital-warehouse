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

mix.js('resources/assets/js/admin.js', 'public/js')
    .js('resources/assets/js/merchant.js', 'public/js')
    .js('resources/assets/js/owner.js', 'public/js')
    .js('resources/assets/js/warehouse.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css');

if (mix.inProduction()) {
    // will automatically append a unique hash to the filenames
    mix.version();
}
