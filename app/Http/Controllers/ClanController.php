<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App;
use DB;
use Cache;
use Carbon\Carbon;

class ClanController extends Controller
{
  public function report() {

    // GET DISCORD USERS THAT HAS NO NICKNAME OR NOT FOUND IN CLAN

    $discord_white_list = ['alv86'];
    $id_white_list = ['4611686018484199706']; // val jordan

    $discord_member_nicknames = DB::connection('ccbbot')->table('member_roles')
      ->whereNotIn('nickname', $discord_white_list)
      ->where('role', 'Member')->get()->pluck('nickname');

    $clan_member_bnet_ids = App\Classes\Clan_Member_Platform_Profile::
      whereNotIn('id', $id_white_list)
      ->get()->pluck('blizzardID');


    // To lower case
    $discord_member_nicknames = $discord_member_nicknames->transform(function($item, $key){
      return strtolower( $item );
    });

    $clan_member_bnet_ids = $clan_member_bnet_ids->transform(function($item, $key){
      return strtolower( $item );
    });

    // Get Diff
    $diff = $discord_member_nicknames->diff( $clan_member_bnet_ids );

    echo "<div><strong>1. Discord members without bnet nickname or not found in clan</strong></div>";

    if( $diff->count() ) {
      echo '<div style="margin-top: 15px;">'. implode('<br/>', $diff->toArray()) . '</div>';
    }
    else {

    }

    // GET IN-ACTIVE

    $members = App\Classes\Clan_Member::whereRaw('last_online < NOW() - INTERVAL 2 WEEK')->orderBy('last_online', 'desc')->get();

    echo "<br/>";
    echo "<div><strong>2. In-active members</strong></div>";

    if( $members ) {
      echo '<table style="margin-top: 15px; border-spacing: 0;">';
      echo '<thead><tr style="text-align: left;"><th>Name</th><th>Last Online</th><th>Days</th></tr></thead><tbody>';

      foreach( $members as $member ) {
        echo '<tr>';
        echo '<td>'.$member->display_name . '</td>';
        echo '<td>'.Carbon::parse($member->last_online)->format('j M Y'). '</td>';
        echo '<td>'.Carbon::now('America/Los_Angeles')->diffInDays( Carbon::parse($member->last_online) ).'</td>';
        echo '</tr>';
      }

      echo '</tbody></table>';
      echo '<style>td, th { padding: 0 15px 0 0; } th { padding-bottom: 5px; }</style>';
    }
    else {
      echo "<div>Woohoo, nobody.</div>";
    }

    dd();
  }

  public function member_activities_listing(Request $request, $type, $member_id) {

    $items_per_page = 250;

    $data['active_page'] = 'clan_member_activities_listing';

    $data['clan_members'] = Cache::rememberForever('clan_members', function () {
      return App\Classes\Clan_Member::get();
    });

    $data['member'] = $data['clan_members']->where('id', $member_id)->first();

    $data['activity_definition'] = collect(json_decode(file_get_contents(storage_path('manifest/DestinyActivityDefinition.json'))));

    switch($type) {
      case "pvp":

        $data['activity_type'] = 'PvP';
        $data['link'] = route('clan_member_activities_listing', ['pvp', $member_id]);

        $data['activity_instances'] = App\Classes\Clan_Member_Activity_Buddy_Instance::
          with('pgcr')
          ->where('mode', 5)
          ->where('member_id', $member_id)
          ->orderBy('activity_id', 'desc')
          ->groupBy('activity_id')
          ->paginate($items_per_page);

        break;

      case "raid":

        $data['activity_type'] = 'Raid';
        $data['link'] = route('clan_member_activities_listing', ['raid', $member_id]);

        $data['activity_instances'] = App\Classes\Clan_Member_Activity_Buddy_Instance::
          with('pgcr')
          ->where('mode', 4)
          ->where('member_id', $member_id)
          ->orderBy('activity_id', 'desc')
          ->groupBy('activity_id')
          ->paginate($items_per_page);

        break;

      case "gambit":

        $data['activity_type'] = 'Gambit';
        $data['link'] = route('clan_member_activities_listing', ['gambit', $member_id]);

        $data['activity_instances'] = App\Classes\Clan_Member_Activity_Buddy_Instance::
          with('pgcr')
          ->whereIn('mode', [63, 75])
          ->where('member_id', $member_id)
          ->orderBy('activity_id', 'desc')
          ->groupBy('activity_id')
          ->paginate($items_per_page);

        break;

      case "all":
      default:

        $data['activity_type'] = 'All';
        $data['link'] = route('clan_member_activities_listing', ['all', $member_id]);

        $data['activity_instances'] = App\Classes\Clan_Member_Activity_Buddy_Instance::
          with('pgcr')
          ->where('member_id', $member_id)
          ->orderBy('activity_id', 'desc')
          ->groupBy('activity_id')
          ->paginate($items_per_page);
    }

    return view('clan.member_activities', $data);
  }

  public function activities() {
    $data['site_title'] = 'Past activities for the ' . env('SITE_NAME') .' Clan in Destiny 2';
    $data['active_page'] = 'activities';

    $data['members'] = App\Classes\Clan_Member::all();

    return view('clan.activities', $data);
  }

  public function roster() {
    $data['site_title'] = 'Roster for the ' . env('SITE_NAME') .' Clan in Destiny 2';
    $data['active_page'] = 'roster';

    return view('clan.roster', $data);
  }

  public function get_roster() {
    $roster = Cache::rememberForever('clan_members_characters', function () {
      return App\Classes\Clan_Member::with('characters')->with('platform_profile')->get();
    });
    return response()->json($roster);
  }

  public function get_clan_exotic_collection() {
    $data['clan_exotic_weapon_collection'] = Cache::rememberForever('clan_exotic_weapon_collection', function () {
      return DB::table("clan_member_exotic_weapons")->get();
    });
    $data['clan_exotic_armor_collection'] = Cache::rememberForever('clan_exotic_armor_collection', function () {
      return DB::table("clan_member_exotic_armors")->get();
    });
    $data['exotic_definition'] = Cache::rememberForever('exotic_definition', function () {
      return DB::table("exotics")->get();
    });
    return response()->json($data);
  }

  public function clan_exotic_collection() {
    $data['site_title'] = 'Exotic Collection Progress for the ' . env('SITE_NAME') .' Clan in Destiny 2';
    $data['active_page'] = 'clan_exotic';

    return view('clan.exotics', $data);
  }

  public function member_seal_progression($member_id) {

    $member = App\Classes\Clan_Member::find($member_id);

    if( !$member ) {
      return redirect()->route('clan_seal_progression');
    }

    $data['member'] = $member;
    $data['member_id'] = $member_id;
    $data['site_title'] = 'Seal Progress for ' . $member->display_name;
    $data['active_page'] = 'seals_breakdown';

    return view('clan.sealsBreakdown', $data);
  }

  public function clan_seal_progression() {
    $data['site_title'] = 'Seal Completions for the ' . env('SITE_NAME') .' Clan in Destiny 2';
    $data['active_page'] = 'seals';

    return view('clan.sealCompletions', $data);
  }

  public function get_clan_seal_progression() {
    $seal_completions = Cache::rememberForever('clan_seal_completions', function () {
      return App\Classes\Seal_Completions::get();
    });
    return response()->json($seal_completions);
  }

  public function clan_raid_lockout() {

    $data['site_title'] = 'Raid lockouts for the ' . env('SITE_NAME') .' Clan in Destiny 2';
    $data['active_page'] = 'lockouts';

    return view('clan.raidLockouts', $data);
  }

  public function get_clan_raid_lockout() {
    // Get weekly start / end dates (GMT+8)
    $today = \Carbon\Carbon::now();

    if( $today->dayOfWeek == 3 )
      $start_of_week = $today;
    else
      $start_of_week = new \Carbon\Carbon('last wednesday');

    $start_of_week->hour = 1;
    $start_of_week->minute = 0;
    $start_of_week->second = 0;

    //dd($start_of_week);

    $end_of_week = clone $start_of_week;
    $end_of_week->addDays(7);
    $end_of_week->hour = 0;
    $end_of_week->minute = 59;
    $end_of_week->second = 59;

    $data['start_of_week'] = $start_of_week->format('j M Y');
    $data['end_of_week'] = $end_of_week->format('j M Y');
    $data['raid_lockouts'] = Cache::rememberForever('clan_raid_lockouts', function () {
      return App\Classes\Raid_Lockouts::get();
    });

    return response()->json($data);
  }
}
