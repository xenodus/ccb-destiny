<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;

class UpdateGambitStats extends Command
{
    // gambit
    private $infamy_hash = 2772425241;
    private $infamy_reset_hash = 3901785488;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:GambitStats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update clan member\'s gambit stats';

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
        if( App\Classes\Work_Progress::where('type', 'gambit')->where('status', 'running')
              ->whereRaw('start > NOW() - INTERVAL 5 MINUTE')
              ->count() == 0 )
        {
            $this->info('Begin: Gambit stats update');

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

                $n = 1;

                foreach($members as $member) {

                    $this->info('Processing '.$n.' of '.count($members).' members');
                    $n++;

                    // Gambit Stats
                    $member_gambit_stats_response = $client->get(
                        env('BUNGIE_API_ROOT_URL') . '/Destiny2/' .env('BUNGIE_PC_PLATFORM_ID'). '/Account/'.$member->destinyUserInfo->membershipId.'/Character/0/Stats/?groups=0,0&periodType=0&modes=63',
                        ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
                    );

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
                    $member_profile_response = $client->get(
                        env('BUNGIE_API_ROOT_URL') . '/Destiny2/' .env('BUNGIE_PC_PLATFORM_ID'). '/Profile/'.$member->destinyUserInfo->membershipId.'?components=100,202,900',
                        ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
                    );

                    if( $member_profile_response->getStatusCode() == 200 ) {
                        $member_profile = json_decode($member_profile_response->getBody()->getContents());
                        $member_profile = collect($member_profile);

                        // Privacy freaks
                        if( isset($member_profile['Response']->characterProgressions->data) == false )
                          continue;

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
            $this->info('Error: Gambit stats update already in progress');
            return 0;
        }

        $this->info('Completed: Gambit stats update');
        return 1;
    }
}
