<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;
use Cache;

class UpdateLockouts extends Command
{
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

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:lockouts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update raid lockouts of members';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $failures = [];
        $results = [];

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

        $clan_members = \App\Classes\Clan_Member::get();

        $n = 1;

        $this->info('Begin: Raid lockouts update');

        foreach($clan_members as $member) {

            $this->info('Processing '.$n.' of '.count($clan_members).': ' . $member->display_name);
            $n++;

            $error = false;
            $member_raid_lockout = $this->get_default_lockout();

            if( $member->characters->count() ) {

                $this->info('Characters: ' . $member->characters->count());

                foreach( $member->characters as $character ) {

                    $client = new Client(); //GuzzleHttp\Client
                    $characters_activities_response = $client->get(
                      env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Account/'.$member->id.'/Character/'.$character->id.'/Stats/Activities?mode=4&count=250',
                      ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
                    );

                    $this->info('Processing Character ID: ' . $character->id);

                    if( $characters_activities_response->getStatusCode() == 200 ) {

                        $this->info('Retrieved Success: ' . $character->id);

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
                        $this->info('Unable to retrieve activity stats for: ' . $character->id);

                        $error = true;
                        $failures = $characters_activities_response;
                    }
                }
            }

            if( $error == false ) {
                $raid_lockout = new App\Classes\Raid_Lockouts();
                $raid_lockout->id = $member->id;
                $raid_lockout->data = json_encode($member_raid_lockout);
                $raid_lockout->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

                $results[] = $raid_lockout;
            }
        }

        if( count($results) ) {

          DB::table('raid_lockouts')->truncate();

          foreach($results as $raid_lockout) {
            $raid_lockout->save();
          }

          $this->info('Completed: Raid lockouts update');

          // Refresh Cache
          Cache::forget('clan_raid_lockouts');
          Cache::forever('clan_raid_lockouts', App\Classes\Raid_Lockouts::get());

          return 1;
        }

        return 0;
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
