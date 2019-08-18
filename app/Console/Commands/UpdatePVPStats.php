<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;
use Cache;

class UpdatePVPStats extends Command
{
    // pvp
    private $glory_hash = 2000925172; // competitive
    private $valor_hash = 3882308435; // quickplay
    private $valor_reset_hash = 115001349;
    private $gold_medals_hash = [4230088036, 1371679603, 3882642308, 1413337742, 2857093873, 1271667367, 3324094091];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:PVPStats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update clan member\'s pvp stats';

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
        if( App\Classes\Work_Progress::where('type', 'pvp')->where('status', 'running')
              ->whereRaw('start > NOW() - INTERVAL 5 MINUTE')
              ->count() == 0 )
        {
            $this->info('Begin: PVP stats update');

            $work_progress = new App\Classes\Work_Progress();
            $work_progress->type = 'pvp';
            $work_progress->start = date('Y-m-d H:i:s');
            $work_progress->status = 'running';
            $work_progress->save();

            $client = new Client(); //GuzzleHttp\Client

            $members = App\Classes\Clan_Member::get_members();
            $members = collect(json_decode($members));
            $updated_members = collect([]);

            if( $members->count() > 0 ) {

                $n = 1;

                foreach($members as $member) {

                    $this->info('Processing '.$n.' of '.count($members).': ' . $member->destinyUserInfo->displayName);
                    $n++;

                    $member_pvp_stats_response = $client->get(
                        env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Account/'.$member->destinyUserInfo->membershipId.'/Stats/',
                        ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
                    );

                    if( $member_pvp_stats_response->getStatusCode() == 200 ) {

                        $member_pvp_stats = json_decode($member_pvp_stats_response->getBody()->getContents());
                        $member_pvp_stats = collect($member_pvp_stats);

                        if( isset($member_pvp_stats['Response']->mergedAllCharacters->results->allPvP->allTime) == false ) {
                            $this->info("Skipping");
                            continue;
                        }

                        $member->pvpStats['kad'] = $member_pvp_stats['Response']->mergedAllCharacters->results->allPvP->allTime->efficiency->basic->displayValue;
                        $member->pvpStats['kda'] = $member_pvp_stats['Response']->mergedAllCharacters->results->allPvP->allTime->killsDeathsAssists->basic->displayValue;
                        $member->pvpStats['kd'] = $member_pvp_stats['Response']->mergedAllCharacters->results->allPvP->allTime->killsDeathsRatio->basic->displayValue;

                        $member->pvpStats['weaponKillsSuper'] = $member_pvp_stats['Response']->mergedAllCharacters->results->allPvP->allTime->weaponKillsSuper->basic->displayValue;
                        $member->pvpStats['weaponKillsMelee'] = $member_pvp_stats['Response']->mergedAllCharacters->results->allPvP->allTime->weaponKillsMelee->basic->displayValue;
                        $member->pvpStats['weaponKillsGrenade'] = $member_pvp_stats['Response']->mergedAllCharacters->results->allPvP->allTime->weaponKillsGrenade->basic->displayValue;
                    }
                    else {
                        continue;
                    }

                    $member_profile_response = $client->get(
                        env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.$member->destinyUserInfo->membershipId.'?components=100,202,900',
                        ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
                    );

                    if( $member_profile_response->getStatusCode() == 200 ) {
                        $member_profile = json_decode($member_profile_response->getBody()->getContents());
                        $member_profile = collect($member_profile);

                        // Privacy freaks
                        if( isset($member_profile['Response']->characterProgressions->data) == false )
                          continue;

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
                    else {
                        continue;
                    }

                    $updated_members->push($member);
                }

                App\Classes\Pvp_Stats::update_members($updated_members);

                $work_progress->end = date('Y-m-d H:i:s');
                $work_progress->status = 'completed';
                $work_progress->save();
            }
        }
        else {
            $this->info('Error: PVP stats update already in progress');
            return 0;
        }

        // Refresh Cache
        Cache::forget('pvp_stats');
        Cache::forever('pvp_stats', App\Classes\Pvp_Stats::get());

        $this->info('Completed: PVP stats update');
        return 1;
    }
}
