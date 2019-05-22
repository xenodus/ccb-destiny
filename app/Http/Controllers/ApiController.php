<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use App;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Goutte;
use Carbon\Carbon;

class ApiController extends Controller
{
    function get_leviathan_rotation() {

        $leviOrders = [
            '1685065161' => 'Gauntlet > Dogs > Pools',
            '757116822' => 'Gauntlet > Pools > Dogs',
            '417231112' => 'Dogs > Gauntlet > Pools',
            '3446541099' => 'Dogs > Pools > Gauntlet',
            '2449714930' => 'Pools > Gauntlet > Dogs',
            '3879860661' => 'Pools > Dogs > Gauntlet'
        ];

        $client = new Client(); //GuzzleHttp\Client
        $character_response = $client->get( 'https://www.bungie.net/Platform/Destiny2/4/Profile/4611686018474971535/Character/2305843009339205184?components=204', ['headers' => ['X-API-Key' => env('BUNGIE_API')]] );

        if( $character_response->getStatusCode() == 200 ) {

            $character = json_decode($character_response->getBody()->getContents());
            $character = collect($character);

            foreach($leviOrders as $key => $val) {

                $search = collect($character['Response']->activities->data->availableActivities)->filter(function($v, $k) use ($key) {
                    return $v->activityHash == $key;
                });

                if( $search->count() > 0 ) {
                    return response()->json(['order' => $val]);
                }
            }

            return response()->json([]);
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

        $result = [];

        if($vendor_id) {
            $result = DB::table('vendor_sales')
            ->where('vendor_hash', $vendor_id)
            ->orderBy('vendor_hash')
            ->orderBy('itemTypeDisplayName')
            ->get();
        }
        else {
            $result = DB::table('vendor_sales')
            ->orderBy('vendor_hash')
            ->orderBy('itemTypeDisplayName')
            ->get();
        }

        return response()->json($result);
    }

    function get_nightfall() {

        $result = DB::table('active_nightfall')->get();

        return response()->json($result);
    }

    function get_sales_item_perks($vendor_id) {

        // SELECT vendor_sales_item_perks.* FROM `vendor_sales` LEFT JOIN vendor_sales_item_perks ON vendor_sales.id = vendor_sales_item_perks.vendor_sales_id WHERE vendor_sales.vendor_hash = ? AND vendor_sales_item_perks.id IS NOT NULL

        $result = DB::table('vendor_sales')
        ->select('vendor_sales_item_perks.*')
        ->leftJoin('vendor_sales_item_perks', 'vendor_sales.id', '=', 'vendor_sales_item_perks.vendor_sales_id')
        ->where('vendor_sales.vendor_hash', $vendor_id)
        ->whereNotNull('vendor_sales_item_perks.id')
        ->get();

        return response()->json($result);
    }

    public function activity()
    {
        $data = [];

        $client = new Client(); //GuzzleHttp\Client
        $res = $client->get( 'https://destiny.plumbing/en/raw/DestinyActivityDefinition.json' );

        if( $res->getStatusCode() == 200 ) {
            $activity_definitions = collect(json_decode($res->getBody()->getContents()));
            //dd( $activity_definitions->where('hash', '10898844') );
        }

        // 1. Get members online
        $res = $client->get( route('get_members_online') );

        if( $res->getStatusCode() == 200 ) {
            $members_online = collect(json_decode($res->getBody()->getContents()))->toArray();

            if( count($members_online) > 0 ) {
                // 2. Get members'
                foreach($members_online as $member) {

                    $membership_id = $member->membershipId;

                    $res = $client->get( 'https://www.bungie.net/Platform/Destiny2/4/Profile/'.$membership_id.'?components=100,204', ['headers' => ['X-API-Key' => env('BUNGIE_API')]] );

                    if( $res->getStatusCode() == 200 ) {
                        $member_profile = collect(json_decode($res->getBody()->getContents()));

                        $member_activity = new App\Classes\Member_Activity();
                        $member_activity->displayName = $member_profile['Response']->profile->data->userInfo->displayName;
                        $member_activity->lastSeen = Carbon::parse($member_profile['Response']->profile->data->dateLastPlayed)->timezone('Asia/Singapore')->format('g:i A');

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

        return response()->json($data);
    }
}