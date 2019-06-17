const mix = require('laravel-mix');

// RPG AWESOME
mix.copy('node_modules/rpg-awesome/fonts/*', 'public/css/fonts/');
// FONT AWESOME
mix.copy('node_modules/@fortawesome/fontawesome-free/webfonts/*', 'public/css/webfonts/');

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
], 'public/css/compiled/common.css');

// GUIDES CSS
mix.styles([
    'public/css/post.css',
], 'public/css/compiled/post.css');

// STORE CSS
mix.styles([
    'public/css/store.css',
], 'public/css/compiled/store.css');

// RAID LOCKOUTS CSS
mix.styles([
    'public/css/clan.css',
], 'public/css/compiled/clan.css');

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
    'public/js/index.js',
    'public/js/index_news.js',
    'public/js/index_guides.js',
], 'public/js/compiled/index.js');

// RAID LOCKOUTS PAGE
mix.scripts([
    'public/js/clan/lockouts.js',
], 'public/js/compiled/lockouts.js');

// SEAL COMPLETIONS PAGE
mix.scripts([
    'public/js/clan/seals.js',
], 'public/js/compiled/seals.js');

// POST + LISTING PAGE
mix.scripts([
    'public/js/mtg/mtgtooltip.js',
    'public/js/mtg_top_decks.js',
    'public/js/post_listing.js',
], 'public/js/compiled/post.js');

// STATS PAGES
mix.scripts([
    'public/js/raid_stats.js',
], 'public/js/compiled/raid_stats.js');

mix.scripts([
    'public/js/weapon_stats.js',
], 'public/js/compiled/weapon_stats.js');

mix.scripts([
    'public/js/pve_stats.js',
], 'public/js/compiled/pve_stats.js');

mix.scripts([
    'public/js/pvp_stats.js',
], 'public/js/compiled/pvp_stats.js');

mix.scripts([
    'public/js/gambit_stats.js',
], 'public/js/compiled/gambit_stats.js');

mix.version();
