<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function test()
    {
        $data['site_title'] = env('SITE_NAME');
        $data['active_page'] = 'home';

        return view('test', $data);
    }

    public function home()
    {
        $data['site_title'] = env('SITE_NAME');
        $data['active_page'] = 'home';

        return view('home', $data);
    }

    public function outbreak()
    {
        $data['site_title'] = env('SITE_NAME');
        $data['active_page'] = 'outbreak';

        return view('outbreak', $data);
    }
}