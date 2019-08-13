<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use Cookie;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Classes\Post;

class HomeController extends Controller
{
    public function test(Request $request)
    {
      dd( App\Classes\Clan_Member::get_members() );

      // Get all clan members ID
      $members = App\Classes\Clan_Member::get();
      $member_ids = $members->pluck('id')->all();

      // Activity Modes
      $activity_mode_definitions = collect(json_decode(file_get_contents(storage_path('manifest/DestinyActivityModeDefinition.json'))));

      // 0 == All
      // 4 == Raid
      // 5 == PvP
      // 7 == PvE

      $activity_count = [];
      $page_no = 0;
      $per_page = 100;
      $next_page = false;

      $account_id = env('DESTINY_ID');
      $character_id = env('DESTINY_CHAR_ID');

      $client = new Client();

      $activities_url = env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Account/'.env('DESTINY_ID').'/Character/'.env('DESTINY_CHAR_ID').'/Stats/Activities/?count='.$per_page.'&page=' . $page_no;

      $response = $client->get($activities_url, ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]);

      if( $response->getStatusCode() == 200 ) {
        $response = collect( json_decode($response->getBody()->getContents()) );

        if( isset( $response['Response']->activities ) ) {
          $next_page = true;

          foreach( $response['Response']->activities as $activity ) {
            $activity_id = $activity->activityDetails->instanceId;

            $pgcr_url = env('BUNGIE_API_ROOT_URL').'/Destiny2/Stats/PostGameCarnageReport/'.$activity_id.'/';

            $pgcr_response = $client->get($pgcr_url, ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]);

            if( $pgcr_response->getStatusCode() == 200 ) {
              $pgcr_response = collect( json_decode($pgcr_response->getBody()->getContents()) );

              if( isset($pgcr_response['Response']->entries) ) {
                foreach( $pgcr_response['Response']->entries as $entry ) {
                  // Ignore self
                  if( $entry->player->destinyUserInfo->membershipId != $account_id ) {

                    $player_id = $entry->player->destinyUserInfo->membershipId;

                    foreach( $pgcr_response['Response']->activityDetails->modes as $mode ) {

                      // Individual Modes
                      if( $mode != 0 ) {
                        // Mode Definition
                        $activity_count[ $mode ]['mode'] = $activity_mode_definitions->where('modeType', $mode)->first();

                        if( isset( $activity_count[ $mode ][ $player_id ] ) ) {
                          $activity_count[ $mode ]['players'][ $player_id ]++;
                        }
                        else {
                          $activity_count[ $mode ]['players'][ $player_id ] = 1;
                        }
                      }

                      // All Modes
                      $activity_count[0]['mode'] = $activity_mode_definitions->where('modeType', 0)->first();

                      if( isset( $activity_count[0][ $player_id ] ) ) {
                        $activity_count[0]['players'][ $player_id ]++;
                      }
                      else {
                        $activity_count[0]['players'][ $player_id ] = 1;
                      }
                    }
                  }
                }
              }
            }

            // dd( $activity_id );
          }

          dd( $activity_count );
          dd( $response['Response']->activities );
        }
      }

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
      $data['raids_completed'] = App\Classes\Raid_Stats::get_total_raids_completed();
      $data['pve_kills'] = App\Classes\Pve_Stats::get_total_kills();
      $data['clan_members_count'] = App\Classes\Clan_Member::count();

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