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

/*
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');
 */

// COMMON CSS
mix.styles([
    'public/css/normalize.css',
    'node_modules/bootstrap/dist/css/bootstrap.css',
    'node_modules/rpg-awesome/css/rpg-awesome.css',
    'node_modules/@fortawesome/fontawesome-free/css/all.css',
    'node_modules/animate.css/animate.css',
    'node_modules/lightbox2/src/css/lightbox.css',
    'node_modules/tabulator-tables/dist/css/tabulator.css',
    'node_modules/tabulator-tables/dist/css/tabulator_site.css',
    'public/css/style.css',
], 'public/css/compiled/common.css').version();

// COMMON JS
mix.scripts([
    'node_modules/jquery/dist/jquery.js',
    //'node_modules/popper.js/dist/popper.js',
    'node_modules/bootstrap/dist/js/bootstrap.js',
    'node_modules/moment/moment.js',
    'node_modules/lightbox2/src/js/lightbox.js',
    'node_modules/tabulator-tables/dist/js/tabulator.js',
    'public/js/common.js',
], 'public/js/compiled/common.js').version();

// HOME PAGE
mix.scripts([
    'public/js/index.js',
], 'public/js/compiled/index.js').version();

// STATS PAGES
mix.scripts([
    'public/js/raid_stats.js',
], 'public/js/compiled/raid_stats.js').version();

mix.scripts([
    'public/js/weapon_stats.js',
], 'public/js/compiled/weapon_stats.js').version();

mix.scripts([
    'public/js/pve_stats.js',
], 'public/js/compiled/pve_stats.js').version();

mix.scripts([
    'public/js/pvp_stats.js',
], 'public/js/compiled/pvp_stats.js').version();

mix.scripts([
    'public/js/gambit_stats.js',
], 'public/js/compiled/gambit_stats.js').version();
