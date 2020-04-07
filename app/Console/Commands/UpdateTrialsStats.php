<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;
use Cache;

class UpdateTrialsStats extends Command
{
    // pvp
    private $trials_mode = 84;
    private $trials_triumph_hash = [
        2070128778,
        3697759687,
        1195683851,
        3397654784,
        982436517,
        1990393545,
        2579956138,
        2978283116,
        3874565691,
        2365368886,
        796769783,
        850062451,
        436749534,
        1579233587,
        119340315,
        1655637863,
        2221023950,
        841536982,
        4033724071,
        2377487750
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:TrialsStats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update clan member\'s trials stats';

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
        if( App\Classes\Work_Progress::where('type', 'trials')->where('status', 'running')
              ->whereRaw('start > NOW() - INTERVAL 5 MINUTE')
              ->count() == 0 )
        {
            $this->info('Begin: Trials stats update');

            $work_progress = new App\Classes\Work_Progress();
            $work_progress->type = 'trials';
            $work_progress->start = date('Y-m-d H:i:s');
            $work_progress->status = 'running';
            $work_progress->save();

            $client = new Client(); //GuzzleHttp\Client

            $members = App\Classes\Clan_Member::get_members();
            $members = collect(json_decode($members))->shuffle();
            $updated_members = collect([]);

            if( $members->count() > 0 ) {

                $n = 1;

                foreach($members as $member) {

                    $this->info('Processing '.$n.' of '.count($members).': ' . $member->destinyUserInfo->displayName);
                    $n++;

                    $member_trials_stats_response = $client->get(
                        env('BUNGIE_API_ROOT_URL').'/Destiny2/'.$member->destinyUserInfo->membershipType.'/Account/'.$member->destinyUserInfo->membershipId.'/Character/0/Stats?modes=' . $this->trials_mode,
                        ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
                    );

                    if( $member_trials_stats_response->getStatusCode() == 200 ) {

                        $member_trials_stats = json_decode($member_trials_stats_response->getBody()->getContents());
                        $member_trials_stats = collect($member_trials_stats);

                        if( isset($member_trials_stats['Response']->trials_of_osiris->allTime) == false ) {
                            $this->info("Skipping");
                            continue;
                        }

                        $member->trialsStats['kad'] = $member_trials_stats['Response']->trials_of_osiris->allTime->efficiency->basic->displayValue;
                        $member->trialsStats['kda'] = $member_trials_stats['Response']->trials_of_osiris->allTime->killsDeathsAssists->basic->displayValue;
                        $member->trialsStats['kd'] = $member_trials_stats['Response']->trials_of_osiris->allTime->killsDeathsRatio->basic->displayValue;

                        $member->trialsStats['activitiesEntered'] = $member_trials_stats['Response']->trials_of_osiris->allTime->activitiesEntered->basic->displayValue;
                        $member->trialsStats['activitiesWon'] = $member_trials_stats['Response']->trials_of_osiris->allTime->activitiesWon->basic->displayValue;
                        $member->trialsStats['weaponBestType'] = $member_trials_stats['Response']->trials_of_osiris->allTime->weaponBestType->basic->displayValue;
                        $member->trialsStats['combatRating'] = $member_trials_stats['Response']->trials_of_osiris->allTime->combatRating->basic->displayValue;

                        $member->trialsStats['weaponKillsSuper'] = $member_trials_stats['Response']->trials_of_osiris->allTime->weaponKillsSuper->basic->displayValue;
                        $member->trialsStats['weaponKillsMelee'] = $member_trials_stats['Response']->trials_of_osiris->allTime->weaponKillsMelee->basic->displayValue;
                        $member->trialsStats['weaponKillsGrenade'] = $member_trials_stats['Response']->trials_of_osiris->allTime->weaponKillsGrenade->basic->displayValue;
                    }
                    else {
                        continue;
                    }

                    // To get flawless counts via triumphs

                    $member_profile_response = $client->get(
                        env('BUNGIE_API_ROOT_URL').'/Destiny2/'.$member->destinyUserInfo->membershipType.'/Profile/'.$member->destinyUserInfo->membershipId.'?components=104,900',
                        ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
                    );

                    if( $member_profile_response->getStatusCode() == 200 ) {
                        $member_profile = json_decode($member_profile_response->getBody()->getContents());
                        $member_profile = collect($member_profile);

                        // Privacy freaks
                        if( isset($member_profile['Response']->profileRecords->data) == false )
                          continue;

                        $member->trialsStats['flawless'] = 0;

                        foreach($this->trials_triumph_hash as $triumph_hash) {
                            if( isset($member_profile['Response']->profileRecords->data->records->$triumph_hash->objectives[0]->progress) ) {
                                $member->trialsStats['flawless'] += $member_profile['Response']->profileRecords->data->records->$triumph_hash->objectives[0]->progress;
                            }
                        }
                    }
                    else {
                        continue;
                    }

                    $updated_members->push($member);
                }

                App\Classes\Trials_Stats::update_members($updated_members);

                $work_progress->end = date('Y-m-d H:i:s');
                $work_progress->status = 'completed';
                $work_progress->save();
            }
        }
        else {
            $this->info('Error: Trials stats update already in progress');
            return 0;
        }

        // Refresh Cache
        Cache::forget('trials_stats');
        Cache::forever('trials_stats', App\Classes\Trials_Stats::get());

        $this->info('Completed: Trials stats update');
        return 1;
    }
}
