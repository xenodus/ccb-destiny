<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;

class UpdateClanActivityBuddies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:clanActivityBuddies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update members\' activity buddies';

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
        $client = new Client(['http_errors' => false, 'verify' => false]);

        // Activity Modes
        $activity_mode_definitions = collect(json_decode(file_get_contents(storage_path('manifest/DestinyActivityModeDefinition.json'))));

        $members = App\Classes\Clan_Member::get();
        $member_ids = $members->pluck('id')->all();

        if( $members->count() > 0 ) {

            $this->info('Begin: Clan Activity Buddy');

            $member_index = 1;

            foreach($members as $member) {

                // Helper Text
                $this->info('----------------------------');
                $this->info('Processing '.$member_index.' of '.$members->count().' members: ' . $member->display_name);
                $member_index++;

                //$activity_count = [];
                $per_page = 250;

                $account_id = $member->id;

                $character_index = 1;

                foreach( $member->characters as $character ) {

                    // Helper Text
                    $this->info('----------------------------');
                    $this->info('Processing '.$character_index.' of '.$member->characters->count().' characters: ' . $character->id);
                    $character_index++;

                    $character_id = $character->id;

                    // Defaults
                    $page_no = 0;
                    $next_page = false;
                    $first_date = '';
                    $activity_count = [];

                    while( $page_no == 0 || $next_page == true ) {

                        // Helper Text
                        $this->info('Page no: '.$page_no);

                        $activities_url = env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Account/'.$account_id.'/Character/'.$character_id.'/Stats/Activities/?mode=4&count='.$per_page.'&page=' . $page_no;

                        $next_page = false;
                        $page_no++;

                        $response = $client->get(
                            $activities_url,
                            ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
                        );

                        if( $response->getStatusCode() == 200 ) {

                            $response = collect( json_decode($response->getBody()->getContents()) );

                            if( isset( $response['Response']->activities ) ) {
                                $next_page = true;

                                $activities_index = 1;

                                foreach( $response['Response']->activities as $activity ) {

                                    // Check if activity has already been processed previously
                                    $prev = DB::table('clan_member_activity_last_processed')->where('character_id', $character_id)->first();

                                    if( $prev ) {
                                        $prev_processed_date = \Carbon\Carbon::parse($prev->last_entry);
                                        $current_entry = \Carbon\Carbon::parse($activity->period);

                                        $this->info( $prev_processed_date->format('Y-m-d H:i:s') );
                                        $this->info( $current_entry->format('Y-m-d H:i:s') );

                                        if( $prev_processed_date->lessThan($current_entry) ) {
                                            $next_page = false;
                                            break 2; // skip to next character
                                        }
                                    }

                                    // Get newest activity date to record activities that have been processed
                                    if( $first_date == '' ) {
                                        $first_date = \Carbon\Carbon::parse( $activity->period )->format('Y-m-d H:i:s');
                                    }

                                    // Helper Text
                                    $this->info('Processing '.$activities_index.' of '.count($response['Response']->activities).' activities');
                                    $activities_index++;

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

                                                        // Just Raids for now...
                                                        if( $mode == 4 ) {
                                                            // Mode Definition
                                                            $activity_count[ $mode ]['mode'] = $activity_mode_definitions->where('modeType', $mode)->first();

                                                            if( isset( $activity_count[ $mode ]['players'][ $player_id ] ) ) {
                                                                $activity_count[ $mode ]['players'][ $player_id ]++;
                                                            }
                                                            else {
                                                                $activity_count[ $mode ]['players'][ $player_id ] = 1;
                                                            }
                                                        }

                                                        /*
                                                        // All Modes
                                                        $activity_count[0]['mode'] = $activity_mode_definitions->where('modeType', 0)->first();

                                                        if( isset( $activity_count[0]['players'][ $player_id ] ) ) {
                                                            $activity_count[0]['players'][ $player_id ]++;
                                                        }
                                                        else {
                                                            $activity_count[0]['players'][ $player_id ] = 1;
                                                        }
                                                        */
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if( $first_date ) {
                        DB::table('clan_member_activity_last_processed')
                            ->updateOrInsert(
                                ['character_id' => $character_id],
                                [
                                    'last_entry' => $first_date,
                                    'date_added' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
                                ]
                            );
                    }

                    foreach( $activity_count as $mode => $data ) {
                        foreach( $data['players'] as $buddy_id => $count ) {

                            $clan_member_activity_buddy = \App\Classes\Clan_Member_Activity_Buddy::where('member_id', $member->id)
                                ->where('mode', $mode)
                                ->where('buddy_id', $buddy_id)
                                ->first();

                            if( $clan_member_activity_buddy ) {
                                $clan_member_activity_buddy->activity_count += $count;
                                $clan_member_activity_buddy->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                                $clan_member_activity_buddy->save();
                            }
                            else {
                                $clan_member_activity_buddy = \App\Classes\Clan_Member_Activity_Buddy::create(
                                  [
                                    'member_id' => $member->id,
                                    'mode' => $mode,
                                    'buddy_id' => $buddy_id,
                                    'activity_count' => $count,
                                    'date_added' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
                                  ]
                                );
                            }
                        }
                    }

                    $this->info('Finished Processing: ' . $character->id);
                }

                // dd( $activity_count );
                // dd( $member->characters );
            }

            $this->info('Completed: Clan Activity Buddy');
        }

        return 0;
    }
}
