const mix = require('laravel-mix');

// RPG AWESOME
mix.copy('node_modules/rpg-awesome/fonts/*', 'public/css/fonts/');
// FONT AWESOME
mix.copy('node_modules/@fortawesome/fontawesome-free/webfonts/*', 'public/css/webfonts/');


/*********************************
        CSS
**********************************/

// COMMON CSS
mix.postCss('public/css/style.css', 'public/css/autoprefix/style.css', [
    require('autoprefixer')()
]);

mix.styles([
    'public/css/normalize.css',
    'node_modules/bootstrap/dist/css/bootstrap.css',
    'node_modules/rpg-awesome/css/rpg-awesome.css',
    'node_modules/@fortawesome/fontawesome-free/css/all.css',
    'node_modules/animate.css/animate.css',
    'node_modules/lightbox2/src/css/lightbox.css',
    'node_modules/tabulator-tables/dist/css/tabulator.css',
    'node_modules/tabulator-tables/dist/css/tabulator_site.css',
    'public/css/autoprefix/style.css',
], 'public/css/compiled/common.css');

// GUIDES CSS
mix.postCss('public/css/post.css', 'public/css/compiled/post.css', [
    require('autoprefixer')()
]);

// STORE CSS
mix.postCss('public/css/store.css', 'public/css/compiled/store.css', [
    require('autoprefixer')()
]);

// CLAN PAGE CSS
mix.postCss('public/css/clan.css', 'public/css/compiled/clan.css', [
    require('autoprefixer')()
]);


/*********************************
        JAVASCRIPT
**********************************/

// COMMON JS
mix.scripts([
    'node_modules/jquery/dist/jquery.js',
    'node_modules/lodash/lodash.js',
    'node_modules/bootstrap/dist/js/bootstrap.js',
    'node_modules/moment/moment.js',
    'node_modules/lightbox2/src/js/lightbox.js',
    'node_modules/tabulator-tables/dist/js/tabulator.js',
    'public/js/common.js',
], 'public/js/compiled/common.js');

// HOME PAGE
mix.scripts([
    'public/js/home/index.js',
    'public/js/home/index_news.js',
    'public/js/home/index_guides.js',
], 'public/js/compiled/index.js');

// GLORY CHEESE PAGE
mix.scripts([
    'public/js/glory_cheese.js',
], 'public/js/compiled/glory_cheese.js');

// RAID LOCKOUTS PAGE
mix.scripts([
    'public/js/clan/lockouts.js',
], 'public/js/compiled/lockouts.js');

// SEAL COMPLETIONS PAGE
mix.scripts([
    'public/js/clan/seals.js',
], 'public/js/compiled/seals.js');

// ROSTER PAGE
mix.scripts([
    'public/js/clan/roster.js',
], 'public/js/compiled/roster.js');

// SEAL PROGRESSION PAGE
mix.scripts([
    'public/js/clan/sealProgression.js',
], 'public/js/compiled/sealProgression.js');

// POST + LISTING PAGE
mix.scripts([
    'public/js/mtg/mtgtooltip.js',
    'public/js/guides/mtg_top_decks.js',
    'public/js/guides/post_listing.js',
], 'public/js/compiled/post.js');

// STATS PAGES
mix.scripts([
    'public/js/stats/raid_stats.js',
], 'public/js/compiled/raid_stats.js');

mix.scripts([
    'public/js/stats/weapon_stats.js',
], 'public/js/compiled/weapon_stats.js');

mix.scripts([
    'public/js/stats/pve_stats.js',
], 'public/js/compiled/pve_stats.js');

mix.scripts([
    'public/js/stats/pvp_stats.js',
], 'public/js/compiled/pvp_stats.js');

mix.scripts([
    'public/js/stats/gambit_stats.js',
], 'public/js/compiled/gambit_stats.js');

mix.version();
