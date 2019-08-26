<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;
use Cache;

class UpdateClanGambitActivityBuddies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:clanGambitActivityBuddies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update members\' gambit activity buddies';

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
        $modeType = 63;
        $per_page = 250;
        $client = new Client(['http_errors' => false, 'verify' => false]);
        $headers = ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false];

        // Activity Modes
        $activity_mode_definitions = collect(json_decode(file_get_contents(storage_path('manifest/DestinyActivityModeDefinition.json'))));

        $members = App\Classes\Clan_Member::orderBy('last_online', 'desc')->get();

        if( $members->count() > 0 ) {

            $this->info('Begin: Clan Gambit Activity Buddy');

            $member_index = 1;

            foreach($members as $member) {

                // Helper Text
                $this->info('----------------------------');
                $this->info('Processing '.($member_index++).' of '.$members->count().' members: ' . $member->display_name);

                $account_id = strval( $member->id );
                $character_index = 1;
                $activities_processed = [];

                foreach( $member->characters as $character ) {

                    // Helper Text
                    $this->info('----------------------------');
                    $this->info('Processing '.($character_index++).' of '.$member->characters->count().' characters: '.$character->id);

                    // Defaults
                    $character_id = strval( $character->id );
                    $page_no = 0;
                    $next_page = false;
                    $first_date = '';

                    while( $page_no == 0 || $next_page == true ) {

                        // Helper Text
                        $this->info('Page no: '.$page_no);

                        $activities_url = env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Account/'.$account_id.'/Character/'.$character_id.'/Stats/Activities/?mode='.$modeType.'&count='.$per_page.'&page='.$page_no;

                        $next_page = false;
                        $page_no++;
                        $response = $client->get($activities_url, $headers);

                        if( $response->getStatusCode() == 200 ) {

                            $response = collect( json_decode($response->getBody()->getContents()) );

                            if( isset( $response['Response']->activities ) ) {

                                $next_page = true;
                                $activities_index = 1;

                                foreach( $response['Response']->activities as $activity ) {

                                    // Check if activity has already been processed previously
                                    $prev = DB::table('clan_member_activity_last_processed')
                                        ->where('character_id', $character_id)
                                        ->where('mode', $modeType)
                                        ->first();

                                    if( $prev ) {
                                        // Getting rid of milli / micro-seconds
                                        $prev_processed_date = \Carbon\Carbon::parse( \Carbon\Carbon::parse($prev->last_entry)->format('Y-m-d H:i:s') );
                                        $current_entry = \Carbon\Carbon::parse( \Carbon\Carbon::parse($activity->period)->format('Y-m-d H:i:s') );

                                        $this->info( $prev_processed_date->format('Y-m-d H:i:s') );
                                        $this->info( $current_entry->format('Y-m-d H:i:s') );

                                        if( $prev_processed_date->greaterThanOrEqualTo($current_entry) ) {
                                            $next_page = false;
                                            break 2; // skip to next character
                                        }
                                    }

                                    // Get newest activity date to record activities that have been processed
                                    if( $first_date == '' ) {
                                        $first_date = \Carbon\Carbon::parse( $activity->period )->format('Y-m-d H:i:s');
                                    }

                                    // Helper Text
                                    $this->info('Processing '.($activities_index++).' of '.count($response['Response']->activities).' activities');

                                    $activity_id = strval( $activity->activityDetails->instanceId );

                                    if( in_array($activity_id, $activities_processed) == false ) {

                                        $activities_processed[] = $activity_id;

                                        $pgcr_url = env('BUNGIE_API_ROOT_URL').'/Destiny2/Stats/PostGameCarnageReport/'.$activity_id.'/';

                                        $pgcr_response = $client->get($pgcr_url, $headers);

                                        if( $pgcr_response->getStatusCode() == 200 ) {
                                            $pgcr_response = collect( json_decode($pgcr_response->getBody()->getContents()) );

                                            if( isset($pgcr_response['Response']->entries) ) {

                                                // When some players change characters and get multiple counts in a single activity...
                                                $players_processed = [];

                                                foreach( $pgcr_response['Response']->entries as $entry ) {

                                                    $player_id = strval( $entry->player->destinyUserInfo->membershipId );

                                                    // Ignore self
                                                    if( $player_id != $account_id ) {

                                                        if( in_array($player_id, $players_processed) == false ) {

                                                            // Save activity
                                                            DB::table('clan_member_activity_buddy_instance')
                                                            ->updateOrInsert(
                                                                [
                                                                    'member_id' => $account_id,
                                                                    'buddy_id' => $player_id,
                                                                    'activity_id' => $activity_id,
                                                                    'mode' => $modeType,
                                                                ],
                                                                [
                                                                    'date_added' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
                                                                ]
                                                            );

                                                            DB::table('clan_member_pgcr')
                                                            ->updateOrInsert(
                                                                [
                                                                    'id' => $activity_id
                                                                ],
                                                                [
                                                                    'pgcr' => collect($pgcr_response['Response'])->toJson(),
                                                                    'date_added' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
                                                                ]
                                                            );

                                                            $players_processed[] = $player_id;
                                                        }

                                                    }
                                                }
                                            }
                                        }
                                    }
                                    else {
                                        $this->info("Already processed on another character.");
                                    }
                                }
                            }
                        }
                    }

                    if( $first_date ) {
                        DB::table('clan_member_activity_last_processed')
                            ->updateOrInsert(
                                ['character_id' => $character_id, 'mode' => $modeType],
                                [
                                    'last_entry' => $first_date,
                                    'date_added' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
                                ]
                            );
                    }

                    $this->info('----------------------------');
                    $this->info('Finished Processing: ' . $character->id);
                }
            }

            // Refresh Cache
            Cache::forget('clan_gambit_buddy');
            // $clan_member_raid_buddies = App\Classes\Clan_Member::with('raid_buddies')->get();

            // Cache::forget('clan_member_raid_buddies');
            // Cache::forever('clan_member_raid_buddies', $clan_member_raid_buddies);

            $this->info('Completed: Clan Gambit Activity Buddy');
        }

        return 0;
    }
}
