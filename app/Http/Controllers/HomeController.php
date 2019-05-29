<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use Carbon\Carbon;

use Corcel\Model\Post as WP;

class HomeController extends Controller
{
    public function test()
    {
        // All published posts
        $destiny_posts = WP::published()->taxonomy('category', 'destiny')->newest()->get();
        $magic_posts = WP::published()->taxonomy('category', 'magic-the-gathering')->newest()->get();

        // featured
        $featured_destiny_posts = WP::published()
            ->taxonomy('post_tag', 'featured')
            ->taxonomy('category', 'destiny')
            ->newest()
            ->get();

        // non featured
        $non_featured_destiny_posts = WP::published()
            ->taxonomy('category', 'destiny')
            ->whereDoesntHave('taxonomies', function($q) {
                $q->where('taxonomy', 'post_tag')
                    ->whereHas('term', function($q) {
                        $q->where('slug', 'featured');
                    });
            })
            ->newest()
            ->get();

        // featured first
        $featured_destiny_posts = $featured_destiny_posts->merge($non_featured_destiny_posts);

        dd($featured_destiny_posts->paginate( 3 ));

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