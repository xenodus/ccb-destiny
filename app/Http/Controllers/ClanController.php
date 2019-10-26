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
  public function member_activities_listing(Request $request, $type, $member_id) {

    $items_per_page = 100;

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
      return App\Classes\Clan_Member::with('characters')->with('platform_profile')->with('aliases')->get();
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
