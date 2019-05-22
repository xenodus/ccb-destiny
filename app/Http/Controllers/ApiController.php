<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Goutte;

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
}