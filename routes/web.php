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

// Route::get('/test', 'HomeController@test')->name('test');

Route::get('/guides', 'GuideController@index')->name('guide_index');
Route::get('/guides/category/{slug}/{id}', 'GuideController@category')->name('guide_category');
Route::get('/guides/{slug}/{id}', 'GuideController@post')->name('guide_post');

Route::get('/', 'HomeController@home')->name('home');

// Stats Display
Route::redirect('/stats', '/stats/raid', 301);
Route::get('/stats/raid', 'StatsController@index')->name('stats_raid');
Route::get('/stats/weapons', 'StatsController@weapons')->name('stats_weapons');
Route::get('/stats/pve', 'StatsController@pve')->name('stats_pve');
Route::get('/stats/pvp', 'StatsController@pvp')->name('stats_pvp');
Route::get('/stats/gambit', 'StatsController@gambit')->name('stats_gambit');

Route::get('/outbreak', 'HomeController@outbreak')->name('outbreak_solution');

// Members & Currently Online Members
Route::get('/bungie/members/get', 'StatsController@get_members')->name('bungie_get_members');
Route::get('/bungie/members/online', 'StatsController@get_members_online')->name('get_members_online');

// Raid Stats
Route::get('/bungie/raid/get', 'StatsController@get_raid_stats')->name('get_raid_stats');
Route::get('/bungie/raid/update', 'StatsController@update_raid_stats')->name('update_raid_stats');

// PvE & Weapon Stats
Route::get('/bungie/pve/update', 'StatsController@update_pve_stats')->name('update_pve_stats');
Route::get('/bungie/pve/get', 'StatsController@get_pve_stats')->name('get_pve_stats');
Route::get('/bungie/weapon/get', 'StatsController@get_weapon_stats')->name('get_weapon_stats');

// PvP
Route::get('/bungie/pvp/update', 'StatsController@update_pvp_stats')->name('update_pvp_stats');
Route::get('/bungie/pvp/get', 'StatsController@get_pvp_stats')->name('get_pvp_stats');

// Gambit
Route::get('/bungie/gambit/update', 'StatsController@update_gambit_stats')->name('update_gambit_stats');
Route::get('/bungie/gambit/get', 'StatsController@get_gambit_stats')->name('get_gambit_stats');