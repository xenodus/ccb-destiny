<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;

class UpdatePVEStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:PVEStats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update clan member\'s PVE stats';

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
        if( App\Classes\Work_Progress::where('type', 'pve')->where('status', 'running')
              ->whereRaw('start > NOW() - INTERVAL 5 MINUTE')
              ->count() == 0 )
        {
            $this->info('Begin: PVE stats update');

            $work_progress = new App\Classes\Work_Progress();
            $work_progress->type = 'pve';
            $work_progress->start = date('Y-m-d H:i:s');
            $work_progress->status = 'running';
            $work_progress->save();

            $client = new Client(['http_errors' => false]); //GuzzleHttp\Client

            $members = App\Classes\Clan_Member::get_members();
            $members = collect(json_decode($members));
            $updated_members = collect([]);

            if( $members->count() > 0 ) {

              $n = 1;

              foreach($members as $member) {

                  $this->info('Processing '.$n.' of '.count($members).' members');
                  $n++;

                  $member->weaponKills = App\Classes\Weapon_Stats::get_default_weapon_kills();
                  $member->pveStats = App\Classes\Pve_Stats::get_default_pve_stats();

                  $member_pve_stats_response = $client->get(
                    env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Account/'.$member->destinyUserInfo->membershipId.'/Stats/',
                    ['headers' => ['X-API-Key' => env('BUNGIE_API')]]
                  );

                  if( $member_pve_stats_response->getStatusCode() == 200 ) {
                      $member_pve_stats = json_decode($member_pve_stats_response->getBody()->getContents());
                      $member_pve_stats = collect($member_pve_stats);

                      foreach($member->weaponKills as $key => $val) {
                        $member->weaponKills[$key] = $member_pve_stats['Response']->mergedAllCharacters->merged->allTime->$key->basic->displayValue;
                      }

                      foreach($member->pveStats as $key => $val) {
                        $member->pveStats[$key] = $member_pve_stats['Response']->mergedAllCharacters->results->allPvE->allTime->$key->basic->displayValue;
                      }

                      $member->charactersDeleted = count(collect($member_pve_stats['Response']->characters)->filter(
                          function($value, $key){ return $value->deleted == true; }
                      ));
                  }
                  else {
                    continue;
                  }

                $updated_members->push($member);
              }

              App\Classes\Weapon_Stats::update_members($updated_members);
              App\Classes\Pve_Stats::update_members($updated_members);

              $work_progress->end = date('Y-m-d H:i:s');
              $work_progress->status = 'completed';
              $work_progress->save();
            }
        }
        else {
            $this->info('Error: PVE stats update already in progress');
            return 0;
        }

        $this->info('Completed: PVE stats update');
        return 1;
    }
}
