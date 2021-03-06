<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;
use Cache;

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

            DB::table('nightfall')->truncate(); // cleanup db

            $activity_definitions = collect(json_decode(file_get_contents(storage_path('manifest/DestinyActivityDefinition.json'))));

            foreach($characters_activities as $activity) {

                // $this->info( $activity_definitions[ $activity->activityHash ]->displayProperties->name . ': ' . $activity->activityHash );

                if( $activity_definitions[ $activity->activityHash ]->activityTypeHash == $this->nightfallActivityTypeHash
                    && count($activity_definitions[ $activity->activityHash ]->modifiers) > 0
                    // && strpos($activity_definitions[ $activity->activityHash ]->displayProperties->name, 'Ordeal') === false
                ) {

                    if( in_array($activity_definitions[ $activity->activityHash ]->displayProperties->name, ['Nightfall: The Ordeal: Hero', 'Nightfall: The Ordeal: Legend', 'Nightfall: The Ordeal: Master']) == false ) {

                        $nf = new App\Classes\Nightfall();

                        if( $activity_definitions[ $activity->activityHash ]->displayProperties->name == 'Nightfall: The Ordeal: Adept' ) {
                            $nf->name = "The Ordeal: " . $activity_definitions[ $activity->activityHash ]->displayProperties->description;
                        }
                        else {
                            $nf->name = str_replace('Nightfall: ', '', $activity_definitions[ $activity->activityHash ]->displayProperties->name);
                        }

                        $nf->description = $activity_definitions[ $activity->activityHash ]->displayProperties->description;
                        $nf->icon = $activity_definitions[ $activity->activityHash ]->displayProperties->icon;
                        $nf->hash = $activity->activityHash;
                        $nf->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                        $nf->save();

                        $this->info('Inserted: ' . $nf->name);
                    }
                }
            }

            // Refresh Cache
            Cache::forget('milestones_nightfall');
            Cache::forever('milestones_nightfall', App\Classes\Nightfall::get());
        }

        $this->info('Completed: weekly nightfall update');
    }
}