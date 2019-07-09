<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;

class StatsController extends Controller
{
  private $bungie_api_root_path = 'https://www.bungie.net/Platform';
  private $raid_report_root_path = 'https://b9bv2wd97h.execute-api.us-west-2.amazonaws.com/prod/api/player/';
  private $clan_id = '3717919';

  // raid
  private $petra_run_hash = '4177910003';
  private $diamond_run_hash = '2648109757';
  private $crown_run_hash = '1558682416';
  private $raid_activity_hash = [
    'levi'  => [2693136600, 2693136601, 2693136602, 2693136603, 2693136604, 2693136605],
    'levip' => [417231112, 757116822, 1685065161, 2449714930, 3446541099, 3879860661],
    'eow'   => [3089205900],
    'eowp'  => [809170886],
    'sos'   => [119944200],
    'sosp'  => [3213556450],
    'lw'    => [2122313384],
    'sotp'  => [548750096],
    'cos'   => [3333172150]
  ];

  // pvp
  private $glory_hash = 2000925172; // competitive
  private $valor_hash = 3882308435; // quickplay
  private $valor_reset_hash = 115001349;
  private $gold_medals_hash = [4230088036, 1371679603, 3882642308, 1413337742, 2857093873, 1271667367, 3324094091];

  // gambit
  private $infamy_hash = 2772425241;
  private $infamy_reset_hash = 3901785488;

  public function test()
  {
    $client = new Client(); //GuzzleHttp\Client

    $member_gambit_stats_response = $client->get('https://stats.bungie.net/Platform/Destiny2/4/Account/4611686018471180200/Stats', ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false
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

  // Update gambit data
  public function update_gambit_stats()
  {
    if( App\Classes\Work_Progress::where('type', 'gambit')->where('status', 'running')
          ->whereRaw('start > NOW() - INTERVAL 5 MINUTE')
          ->count() == 0 )
    {
      $work_progress = new App\Classes\Work_Progress();
      $work_progress->type = 'gambit';
      $work_progress->start = date('Y-m-d H:i:s');
      $work_progress->status = 'running';
      $work_progress->save();

      $client = new Client(); //GuzzleHttp\Client
      $members_response = $client->get( route('bungie_get_members') );

      if( $members_response->getStatusCode() == 200 ) {

        $members = json_decode($members_response->getBody()->getContents());
        $members = collect($members);

        foreach($members as $member) {

          // Gambit Stats
          $member_gambit_stats_response = $client->get($this->bungie_api_root_path.'/Destiny2/4/Account/'.$member->destinyUserInfo->membershipId.'/Character/0/Stats/?groups=0,0&periodType=0&modes=63', ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false
          ]);

          if( $member_gambit_stats_response->getStatusCode() == 200 ) {
            $member_gambit_stats = json_decode($member_gambit_stats_response->getBody()->getContents());
            $member_gambit_stats = collect($member_gambit_stats);

            if( isset($member_gambit_stats['Response']->pvecomp_gambit->allTime) ) {

              $gs = $member_gambit_stats['Response']->pvecomp_gambit->allTime;

              $member->gambitStats['activitiesEntered'] = $gs->activitiesEntered->basic->displayValue;
              $member->gambitStats['activitiesWon'] = $gs->activitiesWon->basic->displayValue;
              $member->gambitStats['kills'] = $gs->kills->basic->displayValue;
              $member->gambitStats['deaths'] = $gs->deaths->basic->displayValue;
              $member->gambitStats['killsDeathsRatio'] = $gs->killsDeathsRatio->basic->displayValue;
              $member->gambitStats['suicides'] = $gs->suicides->basic->displayValue;
              $member->gambitStats['efficiency'] = $gs->efficiency->basic->displayValue;
              $member->gambitStats['invasionKills'] = $gs->invasionKills->basic->displayValue;
              $member->gambitStats['invaderKills'] = $gs->invaderKills->basic->displayValue;
              $member->gambitStats['invaderDeaths'] = $gs->invaderDeaths->basic->displayValue;
              $member->gambitStats['primevalDamage'] = $gs->primevalDamage->basic->displayValue;
              $member->gambitStats['primevalHealing'] = str_replace('%', '', $gs->primevalHealing->basic->displayValue);
              $member->gambitStats['motesDeposited'] = $gs->motesDeposited->basic->displayValue;
              $member->gambitStats['motesDenied'] = $gs->motesDenied->basic->displayValue;
              $member->gambitStats['motesLost'] = $gs->motesLost->basic->displayValue;
              $member->gambitStats['smallBlockersSent'] = $gs->smallBlockersSent->basic->displayValue;
              $member->gambitStats['mediumBlockersSent'] = $gs->mediumBlockersSent->basic->displayValue;
              $member->gambitStats['largeBlockersSent'] = $gs->largeBlockersSent->basic->displayValue;
            }
          }

          // Profile
          $member_profile_response = $client->get( $this->bungie_api_root_path.'/Destiny2/4/Profile/'.$member->destinyUserInfo->membershipId.'?components=100,202,900', ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false
          ]);

          if( $member_profile_response->getStatusCode() == 200 ) {
            $member_profile = json_decode($member_profile_response->getBody()->getContents());
            $member_profile = collect($member_profile);

            $character_id =  key(collect($member_profile['Response']->characterProgressions->data)->toArray());

            $infamy_hash = $this->infamy_hash;
            $infamy_reset_hash = $this->infamy_reset_hash;

            $member->gambitStats['infamy'] = $member_profile['Response']->characterProgressions->data->$character_id->progressions->$infamy_hash->currentProgress;
            $member->gambitStats['infamy_step'] = $member_profile['Response']->characterProgressions->data->$character_id->progressions->$infamy_hash->level;
            $member->gambitStats['infamy_resets'] = isset($member_profile['Response']->profileRecords->data->records->$infamy_reset_hash->objectives[0]->progress) ? $member_profile['Response']->profileRecords->data->records->$infamy_reset_hash->objectives[0]->progress : 0;
          }

          //dd($member);
          //dd($member_gambit_stats);
        }

        App\Classes\Gambit_Stats::update_members($members);

        $work_progress->end = date('Y-m-d H:i:s');
        $work_progress->status = 'completed';
        $work_progress->save();

        return response()->json(['status' => 1]); // success
      }
    }
    else {
      return response()->json(['status' => 2]); // already in progress
    }

    return response()->json(['status' => 0]); // default
  }

  // Update PvP and Weapons data
  public function update_pvp_stats()
  {
    if( App\Classes\Work_Progress::where('type', 'pvp')->where('status', 'running')
          ->whereRaw('start > NOW() - INTERVAL 5 MINUTE')
          ->count() == 0 )
    {
      $work_progress = new App\Classes\Work_Progress();
      $work_progress->type = 'pvp';
      $work_progress->start = date('Y-m-d H:i:s');
      $work_progress->status = 'running';
      $work_progress->save();

      $client = new Client(); //GuzzleHttp\Client
      $members_response = $client->get( route('bungie_get_members') );

      if( $members_response->getStatusCode() == 200 ) {

        $members = json_decode($members_response->getBody()->getContents());
        $members = collect($members);

        foreach($members as $member) {

          $member_pvp_stats_response = $client->get($this->bungie_api_root_path.'/Destiny2/4/Account/'.$member->destinyUserInfo->membershipId.'/Stats/', ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false
          ]);

          if( $member_pvp_stats_response->getStatusCode() == 200 ) {
            $member_pvp_stats = json_decode($member_pvp_stats_response->getBody()->getContents());
            $member_pvp_stats = collect($member_pvp_stats);

            $member->pvpStats['kad'] = $member_pvp_stats['Response']->mergedAllCharacters->results->allPvP->allTime->efficiency->basic->displayValue;
            $member->pvpStats['kda'] = $member_pvp_stats['Response']->mergedAllCharacters->results->allPvP->allTime->killsDeathsAssists->basic->displayValue;
            $member->pvpStats['kd'] = $member_pvp_stats['Response']->mergedAllCharacters->results->allPvP->allTime->killsDeathsRatio->basic->displayValue;
          }

          $member_profile_response = $client->get( $this->bungie_api_root_path.'/Destiny2/4/Profile/'.$member->destinyUserInfo->membershipId.'?components=100,202,900', ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false
          ]);

          if( $member_profile_response->getStatusCode() == 200 ) {
            $member_profile = json_decode($member_profile_response->getBody()->getContents());
            $member_profile = collect($member_profile);

            $character_id =  key(collect($member_profile['Response']->characterProgressions->data)->toArray());
            $valor_reset_hash = $this->valor_reset_hash;

            // Valor Resets
            $member->pvpStats['valor_resets'] = isset($member_profile['Response']->profileRecords->data->records->$valor_reset_hash->objectives[0]->progress) ? $member_profile['Response']->profileRecords->data->records->$valor_reset_hash->objectives[0]->progress : 0;

            // Gold Medals
            $gold_medals = 0;

            foreach($this->gold_medals_hash as $hash) {
              $gold_medals += isset($member_profile['Response']->profileRecords->data->records->$hash->objectives[0]->progress) ? $member_profile['Response']->profileRecords->data->records->$hash->objectives[0]->progress : 0;
            }

            $valor_hash = $this->valor_hash;
            $glory_hash = $this->glory_hash;

            $member->pvpStats['gold_medals'] = $gold_medals;
            $member->pvpStats['valor'] = $member_profile['Response']->characterProgressions->data->$character_id->progressions->$valor_hash->currentProgress;
            $member->pvpStats['glory'] = $member_profile['Response']->characterProgressions->data->$character_id->progressions->$glory_hash->currentProgress;
            $member->pvpStats['glory_step'] = $member_profile['Response']->characterProgressions->data->$character_id->progressions->$glory_hash->level;
            $member->pvpStats['valor_step'] = $member_profile['Response']->characterProgressions->data->$character_id->progressions->$valor_hash->level;
          }
        }

        App\Classes\Pvp_Stats::update_members($members);

        $work_progress->end = date('Y-m-d H:i:s');
        $work_progress->status = 'completed';
        $work_progress->save();

        return response()->json(['status' => 1]); // success
      }
    }
    else {
      return response()->json(['status' => 2]); // already in progress
    }

    return response()->json(['status' => 0]); // default
  }

  // Update PvE and Weapons data
  public function update_pve_stats()
  {
    if( App\Classes\Work_Progress::where('type', 'pve')->where('status', 'running')
          ->whereRaw('start > NOW() - INTERVAL 5 MINUTE')
          ->count() == 0 )
    {
      $work_progress = new App\Classes\Work_Progress();
      $work_progress->type = 'pve';
      $work_progress->start = date('Y-m-d H:i:s');
      $work_progress->status = 'running';
      $work_progress->save();

      $client = new Client(['http_errors' => false]); //GuzzleHttp\Client
      $members_response = $client->get( route('bungie_get_members') );

      if( $members_response->getStatusCode() == 200 ) {

        $members = json_decode($members_response->getBody()->getContents());
        $members = collect($members);

        foreach($members as $member) {

          $member->weaponKills = App\Classes\Weapon_Stats::get_default_weapon_kills();
          $member->pveStats = App\Classes\Pve_Stats::get_default_pve_stats();

          $member_pve_stats_response = $client->get($this->bungie_api_root_path.'/Destiny2/4/Account/'.$member->destinyUserInfo->membershipId.'/Stats/', ['headers' => ['X-API-Key' => env('BUNGIE_API')]
          ]);

          if( $member_pve_stats_response->getStatusCode() == 200 ) {
            $member_pve_stats = json_decode($member_pve_stats_response->getBody()->getContents());
            $member_pve_stats = collect($member_pve_stats);

            foreach($member->weaponKills as $key => $val) {
              $member->weaponKills[$key] = $member_pve_stats['Response']->mergedAllCharacters->merged->allTime->$key->basic->displayValue;
            }

            foreach($member->pveStats as $key => $val) {
              $member->pveStats[$key] = $member_pve_stats['Response']->mergedAllCharacters->results->allPvE->allTime->$key->basic->displayValue;
            }

            $member->charactersDeleted = count(collect($member_pve_stats['Response']->characters)->filter(function($value, $key){ return $value->deleted == true; }));
          }
        }

        App\Classes\Weapon_Stats::update_members($members);
        App\Classes\Pve_Stats::update_members($members);

        $work_progress->end = date('Y-m-d H:i:s');
        $work_progress->status = 'completed';
        $work_progress->save();

        return response()->json(['status' => 1]); // success
      }
    }
    else {
      return response()->json(['status' => 2]); // already in progress
    }

    return response()->json(['status' => 0]); // default
  }

  // Update raid data
  public function update_raid_stats()
  {
    App\Classes\Work_Progress::whereRaw('start < NOW() - INTERVAL 1 HOUR')->delete(); // cleanup db

    if( App\Classes\Work_Progress::where('type', 'raid')->where('status', 'running')
          ->whereRaw('start > NOW() - INTERVAL 5 MINUTE')
          ->count() == 0 )
    {
      $work_progress = new App\Classes\Work_Progress();
      $work_progress->type = 'raid';
      $work_progress->start = date('Y-m-d H:i:s');
      $work_progress->status = 'running';
      $work_progress->save();

      $petra_run_hash = $this->petra_run_hash;
      $diamond_run_hash = $this->diamond_run_hash;
      $crown_run_hash = $this->crown_run_hash;

      $client = new Client(['http_errors' => false]); //GuzzleHttp\Client
      $members_response = $client->get( route('bungie_get_members') );

      if( $members_response->getStatusCode() == 200 ) {

        $members = json_decode($members_response->getBody()->getContents());
        $members = collect($members);

        foreach($members as $member) {

          // Check Triumphs for flawless runs
          $member_profile_response = $client->get( $this->bungie_api_root_path.'/Destiny2/4/Profile/'.$member->destinyUserInfo->membershipId.'?components=100,900', ['headers' => ['X-API-Key' => env('BUNGIE_API')]
          ]);

          if( $member_profile_response->getStatusCode() == 200 ) {
            $member_profile = json_decode($member_profile_response->getBody()->getContents());
            $member_profile = collect($member_profile);

            $member->raidClears['petra'] = $member_profile['Response']->profileRecords->data->records->$petra_run_hash->objectives[0]->progress;

            $member->raidClears['diamond'] = $member_profile['Response']->profileRecords->data->records->$diamond_run_hash->objectives[0]->progress;

            $member->raidClears['crown'] = $member_profile['Response']->profileRecords->data->records->$crown_run_hash->objectives[0]->progress;
          }

          // Check raid.report
          $url = $this->raid_report_root_path . $member->destinyUserInfo->membershipId;
          $raid_report_response = $client->get( $url );

          $member->rr_url = $url;

          if( $raid_report_response->getStatusCode() == 200 ) {
            $raid_report = json_decode($raid_report_response->getBody()->getContents());
            $raid_report = collect($raid_report);
            $activities = $raid_report['response']->activities;

            foreach($activities as $activity) {
              foreach($this->raid_activity_hash as $name => $hash_arr) {
                if( in_array($activity->activityHash, $hash_arr) ) {
                  if( isset($member->raidClears[$name]) )
                    $member->raidClears[$name] += $activity->values->clears;
                  else
                    $member->raidClears[$name] = $activity->values->clears;
                }
              }
            }
          }
          //App\Classes\Raid_Stats::update_members($members);
          //dd($member);
        }

        App\Classes\Raid_Stats::update_members($members);

        $work_progress->end = date('Y-m-d H:i:s');
        $work_progress->status = 'completed';
        $work_progress->save();

        return response()->json(['status' => 1]);
      }
    }
    else {
      return response()->json(['status' => 2]); // already in progress
    }

    return response()->json(['status' => 0]);
  }

  public function get_members_online()
  {
    $client = new Client(['http_errors' => false]); //GuzzleHttp\Client
    $response = $client->get( route('bungie_get_members') );

    if( $response->getStatusCode() == 200 ) {
      $payload = json_decode($response->getBody()->getContents());
      $members = collect($payload);

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
    $url = $this->bungie_api_root_path.'/Destiny2/4/Profile/'.$member_id.'?components=200,204';

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
    $url = $this->bungie_api_root_path.'/Destiny2/4/Profile/'.$member_id.'?components=900';

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
    $url = $this->bungie_api_root_path.'/GroupV2/'.$this->clan_id.'/Members/';

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

      DB::connection('ccb_mysql')->table('clan_members')->whereNotIn('id', $ids)->delete();

      foreach($payload->Response->results as $key => $result) {
        if( $result->destinyUserInfo->membershipType == 4 ) {
          $last_online = \Carbon\Carbon::createFromTimestamp($result->lastOnlineStatusChange, 'UTC');
          $last_online->setTimezone('Asia/Singapore');

          $clan_member = \App\Classes\Clan_Member::updateOrCreate(
            ['id' => $result->destinyUserInfo->membershipId],
            [
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