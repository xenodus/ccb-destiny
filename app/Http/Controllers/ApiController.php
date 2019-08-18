<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use App;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Goutte;
use Carbon\Carbon;
use Cookie;
use Illuminate\Http\Request;
use Cache;

class ApiController extends Controller
{
    // values = key words to search in news api
    const NEWS_CATEGORIES = [
        'destiny' => 'Destiny 2 Bungie',
        'division' => 'Ubisoft Division',
        'magic' => 'mtg arena magic'
    ];

    const HIDE_ACTIVITY = true;

    function get_milestones() {
        $activity_modifiers = Cache::rememberForever('milestones_activity_modifier', function () {
            return App\Classes\Activity_Modifier::get();
        });

        $nightfalls = Cache::rememberForever('milestones_nightfall', function () {
            return App\Classes\Nightfall::get();
        });

        $vendor_sales = Cache::rememberForever('vendor_sales', function () {
            return App\Classes\Vendor_Sales::orderBy('vendor_hash')->get();
        });

        // Xur Items' Perks
        $xur_sales_item_perks = Cache::rememberForever('vendor_sales_item_perks_xur', function () {
            return App\Classes\Vendor_Sales_Item_Perks::whereHas('vendor_sales', function($q) {
                $q->where('vendor_hash', '2190858386');
            })->get();
        });

        $milestones = [
            'milestones' => $activity_modifiers,
            'nightfalls' => $nightfalls,
            'vendor_sales' => $vendor_sales,
            'xur_sales_item_perks' => $xur_sales_item_perks
        ];

        return response()->json($milestones);
    }

    function refresh_cache() {

        // clan members
        Cache::forget('clan_members');
        Cache::forever('clan_members', App\Classes\Clan_Member::get());

        // clan members characters
        Cache::forget('clan_members_characters');
        Cache::forever('clan_members_characters', App\Classes\Clan_Member::with('characters')->get());

        // Stats
        Cache::forget('pve_stats');
        Cache::forever('pve_stats', App\Classes\Pve_Stats::get());
        Cache::forget('weapon_stats');
        Cache::forever('weapon_stats', App\Classes\Weapon_Stats::get());
        Cache::forget('pvp_stats');
        Cache::forever('pvp_stats', App\Classes\Pvp_Stats::get());
        Cache::forget('raid_stats');
        Cache::forever('raid_stats', App\Classes\Raid_Stats::get());
        Cache::forget('gambit_stats');
        Cache::forever('gambit_stats', App\Classes\Gambit_Stats::get());

        // Milestones
        Cache::forget('milestones_activity_modifier');
        Cache::forever('milestones_activity_modifier', App\Classes\Activity_Modifier::get());
        Cache::forget('milestones_nightfall');
        Cache::forever('milestones_nightfall', App\Classes\Nightfall::get());

        // Vendors
        $vendor_sales_item_perks_xur = App\Classes\Vendor_Sales_Item_Perks::whereHas('vendor_sales', function($q) {
            $q->where('vendor_hash', '2190858386');
        })->get();

        Cache::forget('vendor_sales_item_perks_xur');
        Cache::forever('vendor_sales_item_perks_xur', $vendor_sales_item_perks_xur);
        Cache::forget('vendor_sales');
        Cache::forever('vendor_sales', App\Classes\Vendor_Sales::orderBy('vendor_hash')->get());
        Cache::forget('vendor_sales_item_perks');
        Cache::forever('vendor_sales_item_perks', App\Classes\Vendor_Sales_Item_Perks::get());

        // Raid Lockouts
        Cache::forget('clan_raid_lockouts');
        Cache::forever('clan_raid_lockouts', App\Classes\Raid_Lockouts::get());

        // Seals
        Cache::forget('clan_seal_completions');
        Cache::forever('clan_seal_completions', App\Classes\Seal_Completions::get());

        // Exotic Collection
        Cache::forget('clan_exotic_weapon_collection');
        Cache::forever('clan_exotic_weapon_collection', DB::table("clan_member_exotic_weapons")->get());

        Cache::forget('clan_exotic_armor_collection');
        Cache::forever('clan_exotic_armor_collection', DB::table("clan_member_exotic_armors")->get());

        Cache::forget('exotic_definition');
        Cache::forever('exotic_definition', DB::table("exotics")->get());

        // Clan Activity
        Cache::forget('clan_activity');

        return response()->json(1);
    }

    function get_record_definition() {
        $def = file_get_contents( storage_path('manifest/DestinyRecordDefinition.json') );
        return response()->json(json_decode($def, true));
    }

    function get_membership_id_from_bnet_id($bnet_id) {

        $client = new Client(); //GuzzleHttp\Client
        $response = $client->get(
            env('BUNGIE_API_ROOT_URL').'/Destiny2/SearchDestinyPlayer/'.env('BUNGIE_PC_PLATFORM_ID').'/'.urlencode($bnet_id).'/',
            [
                'headers' => ['X-API-Key' => env('BUNGIE_API')],
                'http_errors' => false
            ]
        );

        if( $response->getStatusCode() == 200 ) {

            $data = json_decode($response->getBody()->getContents());
            $data = collect($data);

            if( count($data['Response']) > 0 ) {
                return response()->json($data['Response'][0]);
            }
        }

        return response()->json([]);
    }

    function update_glory_from_db(Request $request) {
        $names = explode(',', $request->input('names'));
        $results = [];

        foreach($names as $name) {
            $member = App\Classes\Clan_Member::where('display_name', $name)->first();

            if( $member ) {
                $results[] = [
                    'name' => $name,
                    'glory' => $member->pvp_stats->glory
                ];
            }
        }

        return response()->json($results);
    }

    function closest_glory(Request $request) {

        $points = explode(',', $request->input('points'));
        $names = explode(',', $request->input('names'));

        if( count($points) && count($names) ) {

            $results = [];
            $smallest_index = -1;

            $combinations = new \drupol\phpermutations\Generators\Combinations($points, 4);
            $combinations = $combinations->toArray();

            // Just Brute Force
            foreach($combinations as $c_key => $c) {
                $team1 = $c;
                $team2 = $points;

                foreach($team1 as $p1) {
                    foreach($team2 as $key => $p2) {
                        if( $p1 == $p2 ) {
                            unset($team2[$key]);
                            break;
                        }
                    }
                }

                $diff = array_sum($team1) - array_sum($team2) > 0 ? array_sum($team1) - array_sum($team2) : abs(array_sum($team1) - array_sum($team2));

                $results[] = [
                    'team1' => $team1,
                    'team2' => array_values($team2),
                    'diff' => $diff,
                    'team1_total' => array_sum($team1),
                    'team2_total' => array_sum($team2),
                ];

                if( $smallest_index == -1 ) {
                    $smallest_index = 0;
                    $smallest_diff = $results[$smallest_index]['diff'];
                }
                elseif( $diff < $smallest_diff ) {
                    $smallest_index = $c_key;
                    $smallest_diff = $diff;
                }
            }

            return response()->json($results[$smallest_index])
                ->cookie(Cookie::forever('glory_names', $request->input('names')))
                ->cookie(Cookie::forever('glory_points', $request->input('points')));
        }

        return response()->json([]);
    }

    function get_mtg_top_decks() {
        $decks = [];
        $client = new Goutte\Client();
        $crawler = $client->request('GET', 'https://www.mtggoldfish.com/metagame/standard#paper');

        if( $crawler->filter('table.table.table-condensed.sample_deck')->count() ) {
            $t = $crawler->filter('table.sample_deck')->each(function($node) use (&$decks) {
                $deck_set['event_name'] = str_replace("\n", " ", trim($node->previousAll()->filter('h4')->first()->text()));
                $deck_set['event_link'] = $node->previousAll()->filter('h4 a')->first()->link()->getUri();

                $node->filter('td.col-deck > span.deck-price-paper')->each(function ($node) use (&$deck_set) {
                    $html = trim(str_replace('<a href="/deck/', '<a target="_blank" href="https://www.mtggoldfish.com/deck/', $node->html()));

                    $deck_set['decks'][] = [
                        'link' => $node->filter('a')->first()->link()->getUri(),
                        'name' => trim($node->text())
                    ];
                });

                if( isset($deck_set['decks']) && count($deck_set['decks']) ) $decks[] = $deck_set;
            });
        }

        return response()->json($decks);
    }

    function get_news($category='') {

        // ALl news
        $cache_time = 15 * 60;
        $all_news = Cache::remember('all_news', $cache_time, function () {
            return App\Classes\News_Feed::where('status', 'active')->get();
        });

        if( in_array($category, array_keys(self::NEWS_CATEGORIES)) ) {
            $news = $all_news->where('category', $category)->take(5);
        }
        else {
            $destiny_news = $all_news->where('status', 'active')->where('category', 'destiny')->take(5);
            $division_news = $all_news->where('status', 'active')->where('category', 'division')->take(5);
            $magic_news = $all_news->where('status', 'active')->where('category', 'magic')->take(5);
            $news = $destiny_news->merge($division_news)->merge($magic_news);
        }

        return response()->json($news);
    }

    function update_news() {
        $topics = self::NEWS_CATEGORIES;

        foreach($topics as $key => $topic) {
            // top-headlines | everything
            // category: business entertainment general health science sports technology
            // language: ar de en es fr he it nl no pt ru se ud zh
            $url = 'https://newsapi.org/v2/everything?q='.urlencode($topic).'&language=en&sortBy=publishedAt&sortBy=popularity&sortBy=relevancy&apiKey='.env('NEWS_API');

            $client = new Client(); //GuzzleHttp\Client
            $news_response = $client->get($url);

            if( $news_response->getStatusCode() == 200 ) {
                $news = json_decode($news_response->getBody()->getContents());
                $news = collect($news);

                //dd($news);

                if( count($news['articles']) ) {

                    DB::table('news_feed')->where('category', $key)->update(['status' => 'expired']);
                    DB::table('news_feed')->where('category', $key)->whereRaw('date_added <= now() - INTERVAL 3 HOUR')->delete();

                    foreach($news['articles'] as $article) {

                        $n = App\Classes\News_Feed::where('status', 'active')->where(function($query) use ($article){
                          $query->where('url', $article->url)->orWhere('title', $article->title);
                        })->count();


                        if($n == 0 && self::is_english($article->title)) {
                            $news = new App\Classes\News_Feed;
                            $news->author = $article->author ?? '';
                            $news->category = $key;
                            $news->status = 'active';
                            $news->source = $article->source->name;
                            $news->date_added = Carbon::now()->format('Y-m-d H:i:s');
                            $news->title = $article->title;
                            $news->description = $article->description;
                            $news->url = $article->url;
                            $news->thumbnail = $article->urlToImage ?? '';
                            $news->date_published = Carbon::parse($article->publishedAt)->format('Y-m-d');
                            $news->save();
                        }
                    }
                }
            }
        }

        $news = App\Classes\News_Feed::where('status', 'active')->get();

        return response()->json($news);
    }

    public static function is_english($str)
    {
        if (strlen($str) != strlen(utf8_decode($str))) {
            return false;
        } else {
            return true;
        }
    }

    function get_xur_location() {
        $location = '';
        $client = new Goutte\Client();
        $crawler = $client->request('GET', 'https://wherethefuckisxur.com/');

        if( $crawler->filter('div.xur-location > h1')->count() ) {
            $location = $crawler->filter('div.xur-location > h1')->text();
        }

        return response()->json(['location' => $location]);
    }

    function get_vendor($vendor_id='') {

        if( $vendor_id )
            $vendor_sales = App\Classes\Vendor_Sales::where('vendor_hash', $vendor_id);
        else
            $vendor_sales = App\Classes\Vendor_Sales::orderBy('vendor_hash');

        if($vendor_id=='672118013')
            $vendor_sales = $vendor_sales->orderBy('cost_name');
        else
            $vendor_sales = $vendor_sales->orderBy('itemTypeDisplayName');

        $vendor_sales = $vendor_sales->get();

        return response()->json($vendor_sales);
    }

    function get_sales_item_perks($vendor_id) {

        $vendor_sales_item_perks = App\Classes\Vendor_Sales_Item_Perks::whereHas('vendor_sales', function($q) use($vendor_id){
            $q->where('vendor_hash', $vendor_id);
        })->get();

        return response()->json($vendor_sales_item_perks);
    }

    public function activity()
    {
        $data = [];
        $cache_time = 60;
        $data = Cache::remember('clan_activity', $cache_time, function () use(&$data) {

            $client = new Client(); //GuzzleHttp\Client

            $activity_definitions = collect(json_decode(file_get_contents(storage_path('manifest/DestinyActivityDefinition.json'))));

            // 1. Get members online
            $members_online = App\Classes\Clan_Member::get_members_online();
            $members_online = collect(json_decode($members_online))->toArray();

            if( count($members_online) > 0 ) {
                // 2. Get members'
                foreach($members_online as $member) {

                    $membership_id = $member->membershipId;

                    if( $membership_id == '4611686018474971535' && self::HIDE_ACTIVITY == true ) continue;

                    $res = $client->get( env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.$membership_id.'?components=100,204', ['headers' => ['X-API-Key' => env('BUNGIE_API')]] );

                    if( $res->getStatusCode() == 200 ) {
                        $member_profile = collect(json_decode($res->getBody()->getContents()));

                        $member_activity = new App\Classes\Member_Activity();
                        $member_activity->displayName = $member_profile['Response']->profile->data->userInfo->displayName;
                        $member_activity->lastSeen = Carbon::parse($member_profile['Response']->profile->data->dateLastPlayed)->timezone('Asia/Singapore')->format('g:i A');

                        if( isset($member_profile['Response']->characterActivities->data) ) {
                            foreach( $member_profile['Response']->characterActivities->data as $character_id => $character_activity ) {
                                if( isset($latest_activity) ) {
                                    $prev_activity_dt = Carbon::parse($latest_activity->dateActivityStarted);
                                    $curr_activity = Carbon::parse($character_activity->dateActivityStarted);

                                    if( $curr_activity->greaterThan($prev_activity_dt) ) {
                                        $latest_activity = $character_activity;
                                        $member_activity->character_id = $character_id;
                                    }
                                }
                                else {
                                    $latest_activity = $character_activity;
                                    $member_activity->character_id = $character_id;
                                }
                            }

                            $member_activity->latestActivityTime = Carbon::parse($latest_activity->dateActivityStarted)->timezone('Asia/Singapore')->format('g:i A');

                            $member_activity->latestActivity = $activity_definitions->where('hash', $latest_activity->currentActivityHash)->first();

                            if( $member_activity->latestActivity ) {
                                // Orbit
                                if( $member_activity->latestActivity->placeHash == '2961497387' ) {
                                    $member_activity->latestActivity->originalDisplayProperties->name = "Orbit";
                                    $member_activity->latestActivity->displayProperties->name = "Orbit";
                                }

                                $data[] = $member_activity;
                            }

                            unset( $latest_activity );
                        }
                    }
                }
            }
            return $data;
        });

        return response()->json($data);
    }
}