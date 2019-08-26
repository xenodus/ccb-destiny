<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;
use Cache;

class StatsController extends Controller
{
  public function gambit_buddy_activities($member_id, $buddy_id)
  {
      $data['member'] = App\Classes\Clan_Member::find($member_id);

      $data['activity_instances'] = App\Classes\Clan_Member_Activity_Buddy_Instance::
        with('pgcr')
        ->whereIn('mode', [63, 75])
        ->where('member_id', $member_id)
        ->where('buddy_id', $buddy_id)
        ->get();

      $data['clan_members'] = Cache::rememberForever('clan_members', function () {
        return App\Classes\Clan_Member::get();
      });

      $data['buddy_id'] = $buddy_id;
      $data['activity_definition'] = collect(json_decode(file_get_contents(storage_path('manifest/DestinyActivityDefinition.json'))));

      $data['site_title'] = 'Gambit activities for '.$data['member']->display_name.' from the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'gambit_buddy_activities';

      return view('stats.gambit_buddy_activities', $data);
  }

  public function gambit_buddy($member_id)
  {
      $cache_timer = 5 * 60;

      $data['member'] = Cache::remember('clan_member_gambit_buddies_' . $member_id, $cache_timer, function () use($member_id) {
        $member = App\Classes\Clan_Member::find($member_id);
        $member->gambit_buddies = $member->get_gambit_buddies(50);

        return $member;
      });

      $data['clan_members'] = Cache::rememberForever('clan_members', function () {
        return App\Classes\Clan_Member::get();
      });

      $data['site_title'] = 'Gambit buddies for '.$data['member']->display_name.' from the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'gambit_buddy';

      return view('stats.gambit_buddy', $data);
  }

  public function clan_gambit_buddy()
  {
      $data['site_title'] = 'Gambit buddies for the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'gambit_buddies';

      $data['members'] = Cache::rememberForever('clan_gambit_buddy', function () {
        $members = App\Classes\Clan_Member::get();

        foreach($members as $m) {
          $m->gambit_buddies = $m->get_gambit_buddies(1);
        }

        return $members;
      });

      return view('stats.clan_gambit_buddies', $data);
  }

  public function pvp_buddy_activities($member_id, $buddy_id)
  {
      $data['member'] = App\Classes\Clan_Member::find($member_id);

      $data['activity_instances'] = App\Classes\Clan_Member_Activity_Buddy_Instance::
        with('pgcr')
        ->where('mode', 5)
        ->where('member_id', $member_id)
        ->where('buddy_id', $buddy_id)
        ->get();

      $data['clan_members'] = Cache::rememberForever('clan_members', function () {
        return App\Classes\Clan_Member::get();
      });

      $data['buddy_id'] = $buddy_id;
      $data['activity_definition'] = collect(json_decode(file_get_contents(storage_path('manifest/DestinyActivityDefinition.json'))));

      $data['site_title'] = 'PvP (Crucible) activities for '.$data['member']->display_name.' from the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'pvp_buddy_activities';

      return view('stats.pvp_buddy_activities', $data);
  }

  public function pvp_buddy($member_id)
  {
      $cache_timer = 5 * 60;

      $data['member'] = Cache::remember('clan_member_pvp_buddies_' . $member_id, $cache_timer, function () use($member_id) {
        $member = App\Classes\Clan_Member::find($member_id);
        $member->pvp_buddies = $member->get_pvp_buddies(50);

        return $member;
      });

      $data['clan_members'] = Cache::rememberForever('clan_members', function () {
        return App\Classes\Clan_Member::get();
      });

      $data['site_title'] = 'PvP (Crucible) buddies for '.$data['member']->display_name.' from the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'pvp_buddy';

      return view('stats.pvp_buddy', $data);
  }

  public function clan_pvp_buddy()
  {
      $data['site_title'] = 'PvP (Crucible) buddies for the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'pvp_buddies';

      $data['members'] = Cache::rememberForever('clan_pvp_buddy', function () {
        $members = App\Classes\Clan_Member::get();

        foreach($members as $m) {
          $m->pvp_buddies = $m->get_pvp_buddies(1);
        }

        return $members;
      });

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

      $data['clan_members'] = Cache::rememberForever('clan_members', function () {
        return App\Classes\Clan_Member::get();
      });

      $data['buddy_id'] = $buddy_id;
      $data['activity_definition'] = collect(json_decode(file_get_contents(storage_path('manifest/DestinyActivityDefinition.json'))));

      $data['site_title'] = 'Raid activities for '.$data['member']->display_name.' from the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'raid_buddy_activities';

      return view('stats.raid_buddy_activities', $data);
  }

  public function raid_buddy($member_id)
  {
      $cache_timer = 5 * 60;

      $data['member'] = Cache::remember('clan_member_raid_buddies_' . $member_id, $cache_timer, function () use($member_id) {
        $member = App\Classes\Clan_Member::find($member_id);
        $member->raid_buddies = $member->get_raid_buddies(50);

        return $member;
      });

      $data['clan_members'] = Cache::rememberForever('clan_members', function () {
        return App\Classes\Clan_Member::get();
      });

      $data['site_title'] = 'Raid buddies for '.$data['member']->display_name.' from the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'raid_buddy';

      return view('stats.raid_buddy', $data);
  }

  public function clan_raid_buddy()
  {
      $data['site_title'] = 'Raid buddies for the ' . env('SITE_NAME') .' Clan in Destiny 2';
      $data['active_page'] = 'raid_buddies';

      $data['members'] = Cache::rememberForever('clan_member_raid_buddies', function () {
        $members = App\Classes\Clan_Member::get();

        foreach($members as $m) {
          $m->raid_buddies = $m->get_raid_buddies(1);
        }

        return $members;
      });

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
    $raid_stats = Cache::rememberForever('raid_stats', function () {
      return App\Classes\Raid_Stats::get();
    });

    return response()->json($raid_stats);
  }

  public function get_pve_stats()
  {
    $pve_stats = Cache::rememberForever('pve_stats', function () {
      return App\Classes\Pve_Stats::get();
    });

    return response()->json($pve_stats);
  }

  public function get_pvp_stats()
  {
    $pvp_stats = Cache::rememberForever('pvp_stats', function () {
      return App\Classes\Pvp_Stats::get();
    });

    return response()->json($pvp_stats);
  }

  public function get_weapon_stats()
  {
    $weapon_stats = Cache::rememberForever('weapon_stats', function () {
      return App\Classes\Weapon_Stats::get();
    });

    return response()->json($weapon_stats);
  }

  public function get_gambit_stats()
  {
    $gambit_stats = Cache::rememberForever('gambit_stats', function () {
      return App\Classes\Gambit_Stats::get();
    });

    return response()->json($gambit_stats);
  }
}