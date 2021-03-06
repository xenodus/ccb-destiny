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
use Goutte;
use Illuminate\Support\Facades\Validator;

use App\Classes\Post;

class HomeController extends Controller
{
    public function test(Request $request)
    {
      $member_streams = App\Classes\Stream::getMemberStreams();

      dd( $member_streams );
    }

    public function process_join_us(Request $request)
    {
      Validator::make($request->all(), [
          'ig_name' => 'required|max:255',
          'nationality' => 'required|max:255',
          'timezone' => 'required|max:255',
          'age' => 'required|max:255',
          'expansion' => 'required|max:255',
          'activity' => 'required|max:255',
          'experience' => 'required|max:255',
          'g-recaptcha-response' => 'required|captcha',
      ])->validate();

      $application = new App\Classes\Application();
      $application->ig_name = $request->input('ig_name');
      $application->nationality = $request->input('nationality');
      $application->timezone = $request->input('timezone');
      $application->age = $request->input('age');
      $application->expansion = $request->input('expansion');
      $application->activity = $request->input('activity');
      $application->experience = $request->input('experience');
      $application->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
      $application->save();

      return response()->json(['status' => 1])  ;
    }

    public function join_us()
    {
      $data['site_title'] = 'Apply to join the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'join_us';

      return view('join_us', $data);
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
      $data['site_title'] = env('SITE_NAME') .' Clan in Destiny 2';
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

    public function weeklies()
    {
      $data['site_title'] = 'Weeklies / Dailies | ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'weeklies';

      return view('weeklies.weeklies', $data);
    }

    public function raids()
    {
      $data['site_title'] = 'Scheduled raid events for the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'raid_events';

      // Raid Events
      $data['raid_events'] = App\Classes\Raid_Event::where('server_id', env('DISCORD_SERVER_ID'))
        ->where('status', 'active')
        ->where('channel_id', '!=', env('DISCORD_FOUNDERS_LFG_CHANNEL_ID')) // ignore founders channel
        ->orderBy('event_date', 'asc')
        ->with('signups')
        ->get();

      return view('raids', $data);
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

    public function outbreak()
    {
      $data['site_title'] = env('SITE_NAME');
      $data['active_page'] = 'outbreak';

      return view('outbreak', $data);
    }
}