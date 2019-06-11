<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use Cookie;
use Carbon\Carbon;

use Corcel\Model\Post as WP;

class HomeController extends Controller
{
    public function test()
    {
      $data['site_title'] = env('SITE_NAME');
      $data['active_page'] = 'home';

      $nf = App\Classes\News_Feed::find(75804);

      dd( strlen($nf->title) );
      //dd( strlen(utf8_decode($nf->title)) );
      dd($nf);

      return view('test', $data);
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

    public function outbreak()
    {
      $data['site_title'] = env('SITE_NAME');
      $data['active_page'] = 'outbreak';

      return view('outbreak', $data);
    }
}