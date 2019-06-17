<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;

class ClanController extends Controller
{
  private $bungie_api_root_path = 'https://www.bungie.net/Platform';

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

  // characters
  private $class_hash = [
    671679327 => 'hunter',
    3655393761 => 'titan',
    2271682572 => 'warlock'
  ];

  public function clan_raid_lockout() {

    $data['site_title'] = 'Raid lockouts for the ' . env('SITE_NAME') .' Clan in Destiny 2';
    $data['active_page'] = 'clan';

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
    $data['raid_lockouts'] = \App\Classes\Raid_Lockouts::get();

    return response()->json($data);
  }

  public function update_clan_raid_lockout() {

    $error = false;
    $failures = [];

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

    $results = [];

    $clan_members = \App\Classes\Clan_Member::get();

    foreach($clan_members as $member) {

      $member_raid_lockout = $this->get_default_lockout();

      if( $member->characters->count() ) {

        foreach( $member->characters as $character ) {

          $client = new Client(); //GuzzleHttp\Client
          $characters_activities_response = $client->get(
            $this->bungie_api_root_path.'/Destiny2/4/Account/'.$member->id.'/Character/'.$character->id.'/Stats/Activities?mode=4&count=250',
            ['headers' => ['X-API-Key' => env('BUNGIE_API')]]
          );

          if( $characters_activities_response->getStatusCode() == 200 ) {
            $characters_activities = json_decode($characters_activities_response->getBody()->getContents());
            $characters_activities = collect($characters_activities);

            if( isset($characters_activities['Response']->activities) ) {

              foreach($characters_activities['Response']->activities as $activity) {
                $activity_date = \Carbon\Carbon::parse($activity->period, 'UTC');
                $activity_date->setTimezone('Asia/Singapore');

                if( $activity_date->gte($start_of_week) ) {
                  foreach($this->raid_activity_hash as $raid => $raid_hashes) {
                    // Is a raid activity!
                    if( in_array($activity->activityDetails->referenceId, $raid_hashes) ) {
                      if( $activity->values->completed->basic->displayValue == 'Yes' ) {
                        $member_raid_lockout[$character->class][$raid] = 1;
                      }
                    }
                  }
                }
                else {
                  // stop iterating if date already passed last reset
                  break;
                }
              }
            }
          }
          else {
            $error = true;
            $failures = $characters_activities_response;
          }
        }
      }

      $raid_lockout = new App\Classes\Raid_Lockouts();
      $raid_lockout->id = $member->id;
      $raid_lockout->data = json_encode($member_raid_lockout);
      $raid_lockout->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

      $results[] = $raid_lockout;
    }

    if( count($results) && $error == false ) {

      DB::connection('ccb_mysql')->table('raid_lockouts')->truncate();

      foreach($results as $raid_lockout) {
        $raid_lockout->save();
      }

      return response()->json(['status' => 1]); // success
    }

    dd($failures);

    return response()->json(['status' => 0]); // success
  }

  private function get_default_lockout() {
    $member_raid_lockout = [];

    foreach( array_values($this->class_hash) as $class ) {

      $member_raid_lockout[ $class ] = [];

      foreach( array_keys($this->raid_activity_hash) as $raid ) {
        $member_raid_lockout[ $class ][ $raid ] = 0;
      }
    }

    return $member_raid_lockout;
  }
}
