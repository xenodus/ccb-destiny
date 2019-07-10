<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;

class UpdateRaidStats extends Command
{
    private $raid_report_root_path = 'https://b9bv2wd97h.execute-api.us-west-2.amazonaws.com/prod/api/player/';

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

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:raidStats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update clan\'s raid stats';

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
        App\Classes\Work_Progress::whereRaw('start < NOW() - INTERVAL 1 HOUR')->delete(); // cleanup db

        if( App\Classes\Work_Progress::where('type', 'raid')->where('status', 'running')
              ->whereRaw('start > NOW() - INTERVAL 5 MINUTE')
              ->count() == 0 )
        {
            $this->info('Begin: Raid stats update');

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

                $n = 1;

                foreach($members as $member) {

                    $this->info('Processing '.$n.' of '.count($members).' members');
                    $n++;

                    // Check Triumphs for flawless runs
                    $member_profile_response = $client->get(
                        env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.$member->destinyUserInfo->membershipId.'?components=100,900',
                        ['headers' => ['X-API-Key' => env('BUNGIE_API')]]
                    );

                    if( $member_profile_response->getStatusCode() == 200 ) {
                        $member_profile = json_decode($member_profile_response->getBody()->getContents());
                        $member_profile = collect($member_profile);

                        $member->raidClears['petra'] = $member_profile['Response']->profileRecords->data->records->$petra_run_hash->objectives[0]->progress ?? 0;

                        $member->raidClears['diamond'] = $member_profile['Response']->profileRecords->data->records->$diamond_run_hash->objectives[0]->progress ?? 0;

                        $member->raidClears['crown'] = $member_profile['Response']->profileRecords->data->records->$crown_run_hash->objectives[0]->progress ?? 0;
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
            }
        }
        else {
            $this->info('Error: Raid stats update already in progress');
            return 0;
        }

        $this->info('Completed: Raid stats update');
        return 1;
    }
}
