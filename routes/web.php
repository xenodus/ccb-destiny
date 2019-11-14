<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
Route::get('/', function(){
  return redirect('https://www.bungie.net/', 302);
});
*/

// Test Page
Route::get('/test', 'HomeController@test')->name('test');

// Activities
Route::get('/clan/activities', 'ClanController@activities')->name('clan_member_activities');
Route::get('/clan/activities/{type}/{member_id}', 'ClanController@member_activities_listing')->name('clan_member_activities_listing');

// Alfred Raid Event Web Create / Edit
Route::get('/alfred/{token}', 'AlfredController@web_create_edit')->name('alfred_web_create_edit');
Route::post('/alfred/{token}', 'AlfredController@web_create_edit_process')->name('alfred_web_create_edit_process');

// Join Us
Route::get('/apply', 'HomeController@join_us')->name('join_us');
Route::post('/apply', 'HomeController@process_join_us')->name('process_join_us');

// Buddies
Route::get('/stats/raid/buddy', 'StatsController@clan_raid_buddy')->name('clan_raid_buddy');
Route::get('/stats/raid/buddy/{member_id}', 'StatsController@raid_buddy')->name('raid_buddy');
Route::get('/stats/raid/buddy/{member_id}/{buddy_id}', 'StatsController@raid_buddy_activities')->name('raid_buddy_activities');

Route::get('/stats/pvp/buddy', 'StatsController@clan_pvp_buddy')->name('clan_pvp_buddy');
Route::get('/stats/pvp/buddy/{member_id}', 'StatsController@pvp_buddy')->name('pvp_buddy');
Route::get('/stats/pvp/buddy/{member_id}/{buddy_id}', 'StatsController@pvp_buddy_activities')->name('pvp_buddy_activities');

Route::get('/stats/gambit/buddy', 'StatsController@clan_gambit_buddy')->name('clan_gambit_buddy');
Route::get('/stats/gambit/buddy/{member_id}', 'StatsController@gambit_buddy')->name('gambit_buddy');
Route::get('/stats/gambit/buddy/{member_id}/{buddy_id}', 'StatsController@gambit_buddy_activities')->name('gambit_buddy_activities');

// Team Glory Balancer
Route::get('/glory', 'HomeController@glory_cheese')->name('glory_cheese');

// Store
Route::any('/stripe/webhook', 'StoreController@stripeWebhook')->name('stripe_webhook');

// CoS Jacket
Route::get('/store/product/1/{slug?}', 'StoreController@cos')->name('product_cos_code');
Route::get('/store/success/{id}', 'StoreController@success')->name('product_purchase_cos_success');
Route::get('/store/cancelled/{id}', 'StoreController@failure')->name('product_purchase_cos_failure');

// Sitemap
Route::get('/sitemap/crawl', 'SitemapController@crawl')->name('sitemap_crawl');
Route::get('/sitemap/generate', 'SitemapController@generate')->name('sitemap_generate');

// Guides / Articles
Route::get('/guides', 'GuideController@index')->name('guide_index');
Route::get('/guides/get/latest', 'GuideController@get_latest')->name('get_latest_guides');
Route::get('/guides/category/{slug}/{id}', 'GuideController@category')->name('guide_category');
Route::get('/guides/{slug}/{id}', 'GuideController@post')->name('guide_post');

// Home / Static Pages / iFrames
Route::get('/', 'HomeController@home')->name('home');
Route::get('/milestones/refresh/{status?}', 'HomeController@setMilestoneRefresh')->name('setMilestoneRefresh');
Route::get('/lightmode/{status?}', 'HomeController@setLightmode')->name('lightmode');

// Stats Display
Route::redirect('/stats', '/stats/raid', 301);
Route::get('/stats/raid', 'StatsController@raid')->name('stats_raid');
Route::get('/stats/weapons', 'StatsController@weapons')->name('stats_weapons');
Route::get('/stats/pve', 'StatsController@pve')->name('stats_pve');
Route::get('/stats/pvp', 'StatsController@pvp')->name('stats_pvp');
Route::get('/stats/gambit', 'StatsController@gambit')->name('stats_gambit');
Route::get('/stats/gambit-prime', 'StatsController@gambit_prime')->name('stats_gambit_prime');

// Raid Events
Route::get('/raids', 'HomeController@raids')->name('raid_events');

// Static / iFrames
Route::get('/outbreak', 'HomeController@outbreak')->name('outbreak_solution');

// Clan Roster
Route::redirect('/clan', '/clan/roster', 301);
Route::get('/clan/roster', 'ClanController@roster')->name('clan_roster');
Route::get('/bungie/roster/get', 'ClanController@get_roster')->name('get_roster');

// Clan Raid Lockouts
Route::get('/clan/lockouts', 'ClanController@clan_raid_lockout')->name('clan_raid_lockout');
Route::get('/clan/lockouts/get', 'ClanController@get_clan_raid_lockout')->name('get_clan_raid_lockout');

// Clan Seal Triumph Completions
Route::get('/clan/seals', 'ClanController@clan_seal_progression')->name('clan_seal_progression');
Route::get('/clan/seals/member/{member_id}', 'ClanController@member_seal_progression')->name('member_seal_progression');
Route::get('/clan/seals/get', 'ClanController@get_clan_seal_progression')->name('get_clan_seal_progression');

// Clan Exotic
Route::get('/clan/exotics', 'ClanController@clan_exotic_collection')->name('clan_exotic');
Route::get('/clan/exotics/get', 'ClanController@get_clan_exotic_collection')->name('get_clan_exotic_collection');

// Raid Stats
Route::get('/bungie/raid/get', 'StatsController@get_raid_stats')->name('get_raid_stats');

// PvE & Weapon Stats
Route::get('/bungie/pve/get', 'StatsController@get_pve_stats')->name('get_pve_stats');
Route::get('/bungie/weapon/get', 'StatsController@get_weapon_stats')->name('get_weapon_stats');

// PvP
Route::get('/bungie/pvp/get', 'StatsController@get_pvp_stats')->name('get_pvp_stats');

// Gambit
Route::get('/bungie/gambit/get', 'StatsController@get_gambit_stats')->name('get_gambit_stats');
Route::get('/bungie/gambit_prime/get', 'StatsController@get_gambit_prime_stats')->name('get_gambit_prime_stats');
