<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use Cookie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Cache;

use App\Classes\Post;

class HomeController extends Controller
{
    public function test(Request $request)
    {
      dd('the end...');

      $data['site_title'] = env('SITE_NAME');
      $data['active_page'] = 'home';

      // Extra Stats
      $data['raids_completed'] = App\Classes\Raid_Stats::get_total_raids_completed();
      $data['pve_kills'] = App\Classes\Pve_Stats::get_total_kills();
      $data['clan_members_count'] = App\Classes\Clan_Member::count();

      return view('test', $data);
    }

    public function raids()
    {
      $data['site_title'] = env('SITE_NAME');
      $data['active_page'] = 'raid_events';

      // Raid Events
      $data['raid_events'] = App\Classes\Raid_Event::where('server_id', env('DISCORD_SERVER_ID'))
        ->where('status', 'active')
        ->orderBy('event_date', 'asc')
        ->with('signups')
        ->get();

      return view('raids', $data);
    }

    public function glory_cheese(Request $request)
    {
      $data['site_title'] = env('SITE_NAME');
      $data['active_page'] = 'Glory Cheese Team Balancer';

      $data['glory_names'] = $request->cookie('glory_names') ? explode(',', $request->cookie('glory_names')) : '';
      $data['glory_points'] = $request->cookie('glory_points') ? explode(',', $request->cookie('glory_points')) : '';

      $data['members'] = App\Classes\Clan_Member::get()->pluck('display_name');

      $data['glory_last_update'] = \Carbon\Carbon::parse( App\Classes\Pvp_Stats::first()->last_updated )->format('g:i A j M Y');

      $data['hide_header'] = 1;
      $data['hide_footer'] = 1;

      return view('glory_cheese', $data);
    }

    public function home()
    {
      $data['site_title'] = env('SITE_NAME');
      $data['active_page'] = 'home';

      // Extra Stats
      $cache_time = 15 * 60;
      $data['raids_completed'] = Cache::remember('home_raids_completed', $cache_time, function () {
        return App\Classes\Raid_Stats::get_total_raids_completed();
      });

      $data['pve_kills'] = Cache::remember('home_pve_kills', $cache_time, function () {
        return App\Classes\Pve_Stats::get_total_kills();
      });

      $data['clan_members'] = Cache::rememberForever('clan_members', function () {
        return App\Classes\Clan_Member::get();
      });

      $data['clan_members_count'] = $data['clan_members']->count();

      return view('home', $data);
    }

    public function setLightmode($status=0)
    {
      if(!$status)
        return response()->json(['status' => 0])->withCookie(Cookie::forget('lightmode'));
      else
        return response()->json(['status' => 1])->withCookie(Cookie::forever('lightmode', 1));
    }

    public function setMilestoneRefresh($status=0)
    {
      if(!$status)
        return response()->json(['status' => 0])->withCookie(Cookie::forget('autoRefreshMilestones'));
      else
        return response()->json(['status' => 1])->withCookie(Cookie::forever('autoRefreshMilestones', 1));
    }

    public function chalice()
    {
      $data['site_title'] = env('SITE_NAME');
      $data['active_page'] = 'Chalice Recipes';

      return view('chalice', $data);
    }

    public function raidReport($memberID)
    {
      $data['site_title'] = env('SITE_NAME');
      $data['active_page'] = 'Raid Report';
      $data['memberID'] = $memberID;

      return view('raidReport', $data);
    }

    public function outbreak()
    {
      $data['site_title'] = env('SITE_NAME');
      $data['active_page'] = 'outbreak';

      return view('outbreak', $data);
    }
}