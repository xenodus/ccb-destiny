<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;

class UpdateNightfalls extends Command
{
    private $nightfallActivityTypeHash = '575572995';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:nightfalls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get and update weekly nightfalls';

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
        $this->info('Begin: weekly nightfall update');

        $url = env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.env('DESTINY_ID').'/Character/'.env('DESTINY_CHAR_ID').'?components=204';

        $client = new Client(['http_errors' => false]); //GuzzleHttp\Client
        $response = $client->get($url, [
          'headers' => [
            'X-API-Key' => env('BUNGIE_API')
          ]
        ]);

        $nightfalls = [];

        if( $response->getStatusCode() == 200 ) {
            $characters_activities = json_decode($response->getBody()->getContents());
            $characters_activities = collect($characters_activities->Response->activities->data->availableActivities)
            ->filter(function($activity){
                return isset($activity->displayLevel) && $activity->displayLevel == 50;
            });

            $res = $client->get( 'https://destiny.plumbing/en/raw/DestinyActivityDefinition.json' );

            if( $res->getStatusCode() == 200 ) {

                DB::connection('ccb_mysql')->table('nightfall')->truncate(); // cleanup db

                $activity_definitions = collect(json_decode($res->getBody()->getContents()));

                foreach($characters_activities as $activity) {
                    if( $activity_definitions[ $activity->activityHash ]->activityTypeHash == $this->nightfallActivityTypeHash && count($activity_definitions[ $activity->activityHash ]->modifiers) > 0 ) {

                        $nf = new App\Classes\Nightfall();
                        $nf->name = $activity_definitions[ $activity->activityHash ]->displayProperties->name;
                        $nf->description = $activity_definitions[ $activity->activityHash ]->displayProperties->description;
                        $nf->icon = $activity_definitions[ $activity->activityHash ]->displayProperties->icon;
                        $nf->hash = $activity->activityHash;
                        $nf->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                        $nf->save();

                        $this->info('Inserted: ' . $nf->name);
                    }
                }
            }
        }

        $this->info('Completed: weekly nightfall update');
    }
}
