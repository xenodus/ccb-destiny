<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;

class StatsController extends Controller
{
  public function pvp_buddy($member_id)
  {
      $data['member'] = App\Classes\Clan_Member::with('pvp_buddies')->find($member_id);
      $data['clan_members'] = App\Classes\Clan_Member::get();

      $data['site_title'] = 'PvP (Crucible) buddies for '.$data['member']->display_name.' from the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'pvp_buddy';

      return view('stats.pvp_buddy', $data);
  }

  public function clan_pvp_buddy()
  {
      $data['site_title'] = 'PvP (Crucible) buddies for the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'pvp_buddies';

      $data['members'] = App\Classes\Clan_Member::with('pvp_buddies')->get();

      return view('stats.clan_pvp_buddies', $data);
  }

  public function raid_buddy_activities($member_id, $buddy_id)
  {
      $data['member'] = App\Classes\Clan_Member::find($member_id);

      $data['activity_instances'] = App\Classes\Clan_Member_Activity_Buddy_Instance::
        with('pgcr')
        ->where('mode', 4)
        ->where('member_id', $member_id)
        ->where('buddy_id', $buddy_id)
        ->get();

      $data['clan_members'] = App\Classes\Clan_Member::get();
      $data['buddy_id'] = $buddy_id;
      $data['activity_definition'] = collect(json_decode(file_get_contents(storage_path('manifest/DestinyActivityDefinition.json'))));

      $data['site_title'] = 'Raid activities for '.$data['member']->display_name.' from the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'raid_buddy_activities';

      return view('stats.raid_buddy_activities', $data);
  }

  public function raid_buddy($member_id)
  {
      $data['member'] = App\Classes\Clan_Member::with('raid_buddies')->find($member_id);
      $data['clan_members'] = App\Classes\Clan_Member::get();

      $data['site_title'] = 'Raid buddies for '.$data['member']->display_name.' from the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'raid_buddy';

      return view('stats.raid_buddy', $data);
  }

  public function clan_raid_buddy()
  {
      $data['site_title'] = 'Raid buddies for the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'raid_buddies';

      $data['members'] = App\Classes\Clan_Member::with('raid_buddies')->get();

      return view('stats.clan_raid_buddies', $data);
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
}