<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;

class StatsController extends Controller
{
  private $clan_id = '3717919';

  public function test()
  {
    dd([]);

    $i = App\Classes\Vendor_Sales::find(205);
    dd($i->perks);

    $client = new Client(); //GuzzleHttp\Client

    $member_gambit_stats_response = $client->get(env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Account/4611686018471180200/Stats', ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false
    ]);

    // Character/2305843009300583068/Stats/AggregateActivityStats/

    if( $member_gambit_stats_response->getStatusCode() == 200 ) {
      $member_gambit_stats = json_decode($member_gambit_stats_response->getBody()->getContents());
      $member_gambit_stats = collect($member_gambit_stats);

      dd($member_gambit_stats);
      dd(collect($member_gambit_stats['Response']->activities)->where('activityHash', '3333172150'));
    }
  }

  public function raid()
  {
      $data['site_title'] = 'Raid stats for the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'stats';

      return view('stats.index', $data);
  }

  public function weapons()
  {
      $data['site_title'] = 'Weapon usage stats for the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'weapons';

      return view('stats.weapons', $data);
  }

  public function pve()
  {
      $data['site_title'] = 'PvE stats for the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'pve';

      return view('stats.pve', $data);
  }

  public function pvp()
  {
      $data['site_title'] = 'PvP stats for the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'pvp';

      return view('stats.pvp', $data);
  }

  public function gambit()
  {
      $data['site_title'] = 'Gambit stats for the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'gambit';

      return view('stats.gambit', $data);
  }

  public function get_raid_stats()
  {
    $raid_stats = App\Classes\Raid_Stats::get();

    return response()->json($raid_stats);
  }

  public function get_pve_stats()
  {
    $pve_stats = App\Classes\Pve_Stats::get();

    return response()->json($pve_stats);
  }

  public function get_pvp_stats()
  {
    $pvp_stats = App\Classes\Pvp_Stats::get();

    return response()->json($pvp_stats);
  }

  public function get_weapon_stats()
  {
    $weapon_stats = App\Classes\Weapon_Stats::get();

    return response()->json($weapon_stats);
  }

  public function get_gambit_stats()
  {
    $gambit_stats = App\Classes\Gambit_Stats::get();

    return response()->json($gambit_stats);
  }

  public function get_members_online()
  {
    $client = new Client(['http_errors' => false]); //GuzzleHttp\Client

    $members = App\Classes\Clan_Member::get_members();
    $members = collect(json_decode($members));

    if( $members->count() > 0 ) {

      $online_members = $members->filter(function($member){
        return $member->isOnline != false;
      });

      $online_members = $online_members->map(function($member){
        return [
          'membershipId' => $member->destinyUserInfo->membershipId,
          'displayName' => $member->destinyUserInfo->displayName,
          'avatar' => $member->bungieNetUserInfo->iconPath,
          'lastOnlineStatusChange' => $member->lastOnlineStatusChange
        ];
      });

      return response()->json( $online_members );
    }

    return response()->json([]);
  }

  // Component definition: https://bungie-net.github.io/multi/schema_Destiny-DestinyComponentType.html#schema_Destiny-DestinyComponentType
  public function get_member_characters($member_id)
  {
    $url = env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.$member_id.'?components=200,204';

    $client = new Client(['http_errors' => false]); //GuzzleHttp\Client
    $response = $client->get($url, [
      'headers' => [
        'X-API-Key' => env('BUNGIE_API')
      ]
    ]);

    if( $response->getStatusCode() == 200 ) {
      $payload = json_decode($response->getBody()->getContents());
      return response()->json( $payload->Response );
    }

    return response()->json([]);
  }

  // https://bungie-net.github.io/multi/schema_Destiny-DestinyRecordState.html#schema_Destiny-DestinyRecordState
  public function get_member_triumphs($member_id)
  {
    $url = env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.$member_id.'?components=900';

    $client = new Client(['http_errors' => false]); //GuzzleHttp\Client
    $response = $client->get($url, [
      'headers' => [
        'X-API-Key' => env('BUNGIE_API')
      ]
    ]);

    if( $response->getStatusCode() == 200 ) {
      $payload = json_decode($response->getBody()->getContents());
      return response()->json( $payload->Response );
    }

    return response()->json([]);
  }

  public function get_members()
  {
    $url = env('BUNGIE_API_ROOT_URL').'/GroupV2/'.$this->clan_id.'/Members/';

    $client = new Client(['http_errors' => false]); //GuzzleHttp\Client
    $response = $client->get($url, [
      'headers' => [
        'X-API-Key' => env('BUNGIE_API')
      ]
    ]);

    if( $response->getStatusCode() == 200 ) {
      $payload = json_decode($response->getBody()->getContents());

      // Get all current member ids and delete those are ain't in clan anymore
      $ids = collect($payload->Response->results)->filter(function($member){
        return $member->destinyUserInfo->membershipType == 4;
      })
      ->map(function($member){
        return $member->destinyUserInfo->membershipId;
      })->toArray();

      DB::table('clan_members')->whereNotIn('id', $ids)->delete();

      foreach($payload->Response->results as $key => $result) {
        if( $result->destinyUserInfo->membershipType == 4 ) {
          $last_online = \Carbon\Carbon::createFromTimestamp($result->lastOnlineStatusChange, 'UTC');
          $last_online->setTimezone('Asia/Singapore');

          $clan_member = \App\Classes\Clan_Member::updateOrCreate(
            ['id' => $result->destinyUserInfo->membershipId],
            [
              'bungie_id' => $result->bungieNetUserInfo->membershipId,
              'display_name' => $result->destinyUserInfo->displayName,
              'last_online' => $last_online->format('Y-m-d H:i:s'),
              'date_added' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            ]
          );
        }
        else {
          unset( $payload->Response->results[$key] );
        }
      }

      // Reset array keys
      $payload->Response->results = array_values($payload->Response->results);

      return response()->json( $payload->Response->results );
    }

    return response()->json([]);
  }
}