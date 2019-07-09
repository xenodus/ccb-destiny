<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use Cookie;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Corcel\Model\Post as WP;

class HomeController extends Controller
{
    public function test(Request $request)
    {
      dd( $request->cookies );

      $data['site_title'] = env('SITE_NAME');
      $data['active_page'] = 'home';

      return view('test', $data);
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