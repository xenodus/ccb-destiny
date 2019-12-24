<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;
use Cache;

class UpdateMilestones extends Command
{
    private $debug_mode = false;
    private $debug_show_detailed_milestones = false;

    // Not found in SK update
    private $nightfallHash = '2171429505';
    private $reckoningHash = '601087286';
    private $strikeHash = '1437935813';

    // Functional
    private $y1PrestigeRaidHash = '2683538554';
    private $leviRaidChallengeHash = '3660836525';
    private $flashpointHash = '463010297';

    private $crucibleRotator = '4191379729';
    private $crucibleCore = '2434762343';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:milestones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get and update milestones';

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
        $this->info('Begin: milestones update');

        $url = env('BUNGIE_API_ROOT_URL').'/Destiny2/Milestones/';

        $client = new Client(['http_errors' => false]); //GuzzleHttp\Client
        $response = $client->get($url, [
          'headers' => [
            'X-API-Key' => env('BUNGIE_API')
          ]
        ]);

        $activity_definitions = collect(json_decode(file_get_contents(storage_path('manifest/DestinyActivityDefinition.json'))));
        $milestone_definitions = collect(json_decode(file_get_contents(storage_path('manifest/DestinyMilestoneDefinition.json'))));
        $modifier_definitions = collect(json_decode(file_get_contents(storage_path('manifest/DestinyActivityModifierDefinition.json'))));
        $item_definitions = collect(json_decode(file_get_contents(storage_path('manifest/DestinyInventoryItemDefinition.json'))));
        $item_category_definitions = collect(json_decode(file_get_contents(storage_path('manifest/DestinyItemCategoryDefinition.json'))));

        if( $response->getStatusCode() == 200 ) {
            $milestones = json_decode($response->getBody()->getContents());
            $milestones = collect($milestones->Response);

            // Print all milestones in console
            if( $this->debug_mode ) {
                foreach( $milestones as $milestone  ) {
                    // dd($milestone);

                    $this->info( $milestone_definitions[ $milestone->milestoneHash ]->displayProperties->name . ': ' .  $milestone->milestoneHash);

                    if( $this->debug_show_detailed_milestones ) {
                        $this->info( print_r($milestone) );
                    }
                }

                dd();
            }

            // Nightfalls
            /*
            if( $milestones->get( $this->nightfallHash ) ) {
                $nightfalls = $milestones->get( $this->nightfallHash );

                DB::table('nightfall')->truncate(); // cleanup db

                foreach( $nightfalls->activities as $activity  ) {
                    if( isset( $activity->modifierHashes ) ) {

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

                // Refresh Cache
                Cache::forget('milestones_nightfall');
                Cache::forever('milestones_nightfall', App\Classes\Nightfall::get());
            }
            */

            // Reckoning Modifiers
            /*
            if( $milestones->get( $this->reckoningHash ) ) {
                DB::table('activity_modifiers')->where('type', 'reckoning')->delete(); // cleanup db

                $reckoning = $milestones->get( $this->reckoningHash );

                foreach($reckoning->activities as $activity) {
                    if( isset( $activity->modifierHashes ) ) {

                        foreach($activity->modifierHashes as $modifier_hash) {
                            $am = new App\Classes\Activity_Modifier();
                            $am->type = 'reckoning';
                            $am->hash = $modifier_hash;
                            $am->description = $modifier_definitions[ $modifier_hash ]->displayProperties->description;
                            $am->name = $modifier_definitions[ $modifier_hash ]->displayProperties->name;
                            $am->icon = $modifier_definitions[ $modifier_hash ]->displayProperties->icon;
                            $am->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                            $am->save();

                            $this->info('Inserted Reckoning Modifier: ' . $am->name);
                        }

                        break;
                    }
                }
            }
            */

            // Y1 Prestige EoW / SOS Modifiers
            if( $milestones->get( $this->y1PrestigeRaidHash ) ) {
                DB::table('activity_modifiers')->where('type', 'y1_prestige_raid')->delete(); // cleanup db

                $y1praid = $milestones->get( $this->y1PrestigeRaidHash );

                foreach($y1praid->activities as $activity) {
                    if( isset( $activity->modifierHashes ) ) {

                        foreach($activity->modifierHashes as $modifier_hash) {

                            $am = new App\Classes\Activity_Modifier();
                            $am->type = 'y1_prestige_raid';
                            $am->hash = $modifier_hash;
                            $am->description = $modifier_definitions[ $modifier_hash ]->displayProperties->description;
                            $am->name = $modifier_definitions[ $modifier_hash ]->displayProperties->name;
                            $am->icon = $modifier_definitions[ $modifier_hash ]->displayProperties->icon;
                            $am->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

                            // Get Armsmaster loadout
                            if( $modifier_definitions[ $modifier_hash ]->displayProperties->name == 'Armsmaster' ) {
                                if( isset($activity->loadoutRequirementIndex) ) {
                                    $activity_loadouts = $activity_definitions[$activity->activityHash]->loadouts;
                                    $current_loadouts = $activity_loadouts[$activity->loadoutRequirementIndex];
                                    $restricted_loadouts = [
                                        'Kinetic' => 'Anything',
                                        'Energy' => 'Anything',
                                        'Power' => 'Anything'
                                    ];

                                    foreach($current_loadouts->requirements as $requirement) {
                                        // Kinetic
                                        if( $requirement->equipmentSlotHash == '1498876634' ) {
                                            $restricted_loadouts['Kinetic'] = $item_category_definitions->where(
                                                'grantDestinySubType',
                                                $requirement->allowedWeaponSubTypes[0]
                                            )->first()->shortTitle;
                                        }

                                        // Energy
                                        if( $requirement->equipmentSlotHash == '2465295065' ) {
                                            $restricted_loadouts['Energy'] = $item_category_definitions->where(
                                                'grantDestinySubType',
                                                $requirement->allowedWeaponSubTypes[0]
                                            )->first()->shortTitle;
                                        }

                                        // Power
                                        if( $requirement->equipmentSlotHash == '953998645' ) {
                                            $restricted_loadouts['Power'] = $item_category_definitions->where(
                                                'grantDestinySubType',
                                                $requirement->allowedWeaponSubTypes[0]
                                            )->first()->shortTitle;
                                        }
                                    }

                                    $am->description = "You've been challenged to wield the following: <br/><br/>Kinetic: ".$restricted_loadouts['Kinetic']."<br/>Energy: ".$restricted_loadouts['Energy']."<br/>Power: ".$restricted_loadouts['Power'];
                                }
                            }

                            $am->save();

                            $this->info('Inserted Y1 Prestige Raid Modifier: ' . $am->name);
                        }

                        break;
                    }
                }
            }

            // Y1 Leviathan Challenge - It's actually a modifier
            if( $milestones->get( $this->leviRaidChallengeHash ) ) {
                DB::table('activity_modifiers')->where('type', 'levi_challenge')->delete(); // cleanup db

                $levi = $milestones->get( $this->leviRaidChallengeHash );

                foreach($levi->activities as $activity) {
                    if( isset( $activity->modifierHashes ) ) {

                        foreach($activity->modifierHashes as $modifier_hash) {
                            $am = new App\Classes\Activity_Modifier();
                            $am->type = 'levi_challenge';
                            $am->hash = $modifier_hash;
                            $am->description = $modifier_definitions[ $modifier_hash ]->displayProperties->description;
                            $am->name = $modifier_definitions[ $modifier_hash ]->displayProperties->name;
                            $am->icon = $modifier_definitions[ $modifier_hash ]->displayProperties->icon;
                            $am->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                            $am->save();

                            $this->info('Inserted Levi Challenge Modifier: ' . $am->name);
                        }

                        break;
                    }
                }
            }

            // Vanguard Strike + Normal Menagerie + Heroic Story
            $character_response = $client->get(
                env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.env('DESTINY_ID').'/Character/'.env('DESTINY_CHAR_ID').'?components=204',
                ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
            );

            if( $character_response->getStatusCode() == 200 ) {

                DB::table('activity_modifiers')->where('type', 'strike')->delete(); // cleanup db

                $character = json_decode($character_response->getBody()->getContents());
                $character = collect($character);

                $strikeHash = '4252456044';

                if( isset($character['Response']) ) {

                    $search = collect($character['Response']->activities->data->availableActivities)->filter(function($v) use ($strikeHash) {
                        return $v->activityHash == $strikeHash;
                    });

                    if( $search->count() > 0 ) {

                        if( isset($search->first()->modifierHashes) ) {
                            foreach($search->first()->modifierHashes as $modifier_hash) {

                                $am = new App\Classes\Activity_Modifier();
                                $am->type = 'strike';
                                $am->hash = $modifier_hash;
                                $am->description = $modifier_definitions[ $modifier_hash ]->displayProperties->description;
                                $am->name = $modifier_definitions[ $modifier_hash ]->displayProperties->name;
                                $am->icon = $modifier_definitions[ $modifier_hash ]->displayProperties->icon;
                                $am->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                                $am->save();

                                $this->info('Inserted Heroic Strike Modifier: ' . $am->name);
                            }
                        }
                    }
                }
            }

            // Crucible Rotator
            if( $milestones->get( $this->crucibleRotator ) ) {
                DB::table('activity_modifiers')->where('type', 'crucible_rotator')->delete(); // cleanup db

                $crucibleRotator = $milestones->get( $this->crucibleRotator );

                if( isset( $crucibleRotator->activities ) ) {

                    if( count($crucibleRotator->activities) ) {

                        foreach($crucibleRotator->activities as $crucibleRotatorActivity) {
                            $am = new App\Classes\Activity_Modifier();
                            $am->type = 'crucible_rotator';
                            $am->hash = $crucibleRotatorActivity->activityHash;
                            $am->description = $activity_definitions[$crucibleRotatorActivity->activityHash]->displayProperties->description;
                            $am->name = $activity_definitions[$crucibleRotatorActivity->activityHash]->displayProperties->name;
                            $am->icon = $activity_definitions[$crucibleRotatorActivity->activityHash]->displayProperties->icon;
                            $am->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                            $am->save();

                            $this->info('Inserted Crucible Rotator: ' . $am->name);
                        }
                    }
                }
            }

            // Crucible Core
            if( $milestones->get( $this->crucibleCore ) ) {
                DB::table('activity_modifiers')->where('type', 'crucible_core')->delete(); // cleanup db

                $crucibleCore = $milestones->get( $this->crucibleCore );

                if( isset( $crucibleCore->activities ) ) {

                    if( count($crucibleCore->activities) ) {

                        foreach($crucibleCore->activities as $crucibleCoreActivity) {
                            $am = new App\Classes\Activity_Modifier();
                            $am->type = 'crucible_core';
                            $am->hash = $crucibleCoreActivity->activityHash;
                            $am->description = $activity_definitions[$crucibleCoreActivity->activityHash]->displayProperties->description;
                            $am->name = $activity_definitions[$crucibleCoreActivity->activityHash]->displayProperties->name;
                            $am->icon = $activity_definitions[$crucibleCoreActivity->activityHash]->displayProperties->icon ?? '';
                            $am->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                            $am->save();

                            $this->info('Inserted Crucible Core: ' . $am->name);
                        }
                    }
                }
            }

            // Flashpoint
            if( $milestones->get( $this->flashpointHash ) ) {
                DB::table('activity_modifiers')->where('type', 'flashpoint')->delete(); // cleanup db

                $flashpoint = $milestones->get( $this->flashpointHash );

                if( isset( $flashpoint->availableQuests ) ) {

                    if( count($flashpoint->availableQuests) ) {
                        $am = new App\Classes\Activity_Modifier();
                        $am->type = 'flashpoint';
                        $am->hash = $flashpoint->availableQuests[0]->questItemHash;
                        $am->description = $item_definitions[ $flashpoint->availableQuests[0]->questItemHash ]->displayProperties->description;
                        $am->name = str_replace('FLASHPOINT: ', '', $item_definitions[ $flashpoint->availableQuests[0]->questItemHash ]->displayProperties->name);
                        $am->icon = $item_definitions[ $flashpoint->availableQuests[0]->questItemHash ]->displayProperties->icon;
                        $am->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                        $am->save();

                        $this->info('Inserted Flashpoint: ' . $am->name);
                    }
                }
            }

            // Activities Available to my Warlock
            $character_response = $client->get(
                env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.env('DESTINY_ID').'/Character/'.env('DESTINY_CHAR_ID').'?components=204',
                ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
            );

            // Leviathan Encounter Order
            if( $character_response->getStatusCode() == 200 ) {

                DB::table('activity_modifiers')->where('type', 'levi_order')->delete(); // cleanup db

                $character = json_decode($character_response->getBody()->getContents());
                $character = collect($character);

                $leviOrders = [
                    '1685065161' => 'Gauntlet > Dogs > Pools',
                    '757116822' => 'Gauntlet > Pools > Dogs',
                    '417231112' => 'Dogs > Gauntlet > Pools',
                    '3446541099' => 'Dogs > Pools > Gauntlet',
                    '2449714930' => 'Pools > Gauntlet > Dogs',
                    '3879860661' => 'Pools > Dogs > Gauntlet'
                ];

                foreach($leviOrders as $key => $val) {

                    if( isset($character['Response']) ) {

                        $search = collect($character['Response']->activities->data->availableActivities)->filter(function($v) use ($key) {
                            return $v->activityHash == $key;
                        });

                        if( $search->count() > 0 ) {
                            $am = new App\Classes\Activity_Modifier();
                            $am->type = 'levi_order';
                            $am->hash = $key;
                            $am->description = $val;
                            $am->name = 'order';
                            $am->icon = '/common/destiny2_content/icons/b8177e166f01c2cd914fc3e925ae902d.png';
                            $am->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                            $am->save();

                            $this->info('Inserted Levi Order: ' . $am->description);
                        }
                    }
                }
            }

            // Reckoning Tier 3 Modifiers
            $character_response = $client->get(
                env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.env('DESTINY_ID').'/Character/'.env('DESTINY_CHAR_ID').'?components=204',
                ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
            );

            if( $character_response->getStatusCode() == 200 ) {

                DB::table('activity_modifiers')->where('type', 'reckoning')->delete(); // cleanup db

                $character = json_decode($character_response->getBody()->getContents());
                $character = collect($character);

                $reckoningHash = '1446606128';

                if( isset($character['Response']) ) {

                    $search = collect($character['Response']->activities->data->availableActivities)->filter(function($v) use ($reckoningHash) {
                        return $v->activityHash == $reckoningHash;
                    });

                    if( $search->count() > 0 ) {

                        if( isset($search->first()->modifierHashes) ) {
                            foreach($search->first()->modifierHashes as $modifier_hash) {

                                $am = new App\Classes\Activity_Modifier();
                                $am->type = 'reckoning';
                                $am->hash = $modifier_hash;
                                $am->description = $modifier_definitions[ $modifier_hash ]->displayProperties->description;
                                $am->name = $modifier_definitions[ $modifier_hash ]->displayProperties->name;
                                $am->icon = $modifier_definitions[ $modifier_hash ]->displayProperties->icon;
                                $am->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                                $am->save();

                                $this->info('Inserted Reckoning Modifier: ' . $am->name);
                            }
                        }
                    }
                }
            }

            // Sundial Modifiers
            $character_response = $client->get(
                env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.env('DESTINY_ID').'/Character/'.env('DESTINY_CHAR_ID').'?components=204',
                ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
            );

            if( $character_response->getStatusCode() == 200 ) {

                DB::table('activity_modifiers')->where('type', 'sundial')->delete(); // cleanup db

                $character = json_decode($character_response->getBody()->getContents());
                $character = collect($character);

                $sundialHash = '787912925'; // Normal Difficulty

                if( isset($character['Response']) ) {

                    $search = collect($character['Response']->activities->data->availableActivities)->filter(function($v) use ($sundialHash) {
                        return $v->activityHash == $sundialHash;
                    });

                    if( $search->count() > 0 ) {

                        if( isset($search->first()->modifierHashes) ) {
                            foreach($search->first()->modifierHashes as $modifier_hash) {

                                $am = new App\Classes\Activity_Modifier();
                                $am->type = 'sundial';
                                $am->hash = $modifier_hash;
                                $am->description = $modifier_definitions[ $modifier_hash ]->displayProperties->description;
                                $am->name = $modifier_definitions[ $modifier_hash ]->displayProperties->name;
                                $am->icon = $modifier_definitions[ $modifier_hash ]->displayProperties->icon;
                                $am->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                                $am->save();

                                $this->info('Inserted Sundial Modifier: ' . $am->name);
                            }
                        }
                    }
                }
            }

            // Heroic Menagerie Modifiers
            $character_response = $client->get(
                env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.env('DESTINY_ID').'/Character/'.env('DESTINY_CHAR_ID').'?components=204',
                ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
            );

            if( $character_response->getStatusCode() == 200 ) {

                DB::table('activity_modifiers')->where('type', 'menagerie')->delete(); // cleanup db

                $character = json_decode($character_response->getBody()->getContents());
                $character = collect($character);

                $menagerieHashes = [
                    '2509539867' => [
                        'name' => 'Hasapiko',
                        'description' => 'The Boss for this week is Hasapiko, a Vex Minotaur. The related flawless triumph is, <u>Break a Leg</u>.'
                    ],
                    '2509539865' => [
                        'name' => 'Pagouri',
                        'description' => 'The Boss for this week is Pagouri, a Vex Hydra. The related flawless triumph is, <u>Lambs to the Slaughter</u>.'
                    ],
                    '2509539864' => [
                        'name' => 'Arunak',
                        'description' => 'The Boss for this week is Arunak, a Hive Ogre. The related flawless triumph is, <u>Uncontrolled Rage</u>.'
                    ],
                ];

                foreach($menagerieHashes as $hash => $menagerieInfo) {

                    if( isset($character['Response']) ) {

                        $search = collect($character['Response']->activities->data->availableActivities)->filter(function($v) use ($hash) {
                            return $v->activityHash == $hash;
                        });

                        if( $search->count() > 0 ) {

                            if( isset($activity_definitions[$hash]->modifiers) ) {

                                // Specific Boss
                                $am = new App\Classes\Activity_Modifier();
                                $am->type = 'menagerie';
                                $am->hash = $hash;
                                $am->description = $menagerieInfo['description'];
                                $am->name = 'Boss: ' . $menagerieInfo['name'];
                                $am->icon = '/common/destiny2_content/icons/52c7544a41c3c7b2d0514991fe77d8b7.png';
                                $am->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                                $am->save();

                                $this->info('Inserted Heroic Menegerie Modifier: ' . $am->name);

                                foreach($activity_definitions[$hash]->modifiers as $modifier) {

                                    $am = new App\Classes\Activity_Modifier();
                                    $am->type = 'menagerie';
                                    $am->hash = $modifier->activityModifierHash;
                                    $am->description = $modifier_definitions[ $modifier->activityModifierHash ]->displayProperties->description;
                                    $am->name = $modifier_definitions[ $modifier->activityModifierHash ]->displayProperties->name;
                                    $am->icon = $modifier_definitions[ $modifier->activityModifierHash ]->displayProperties->icon;
                                    $am->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                                    $am->save();

                                    $this->info('Inserted Heroic Menegerie Modifier: ' . $am->name);
                                }
                            }

                            break;
                        }
                    }
                }
            }

            // Nightmare Hunts
            $character_response = $client->get(
                env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.env('DESTINY_ID').'/Character/'.env('DESTINY_CHAR_ID').'?components=204',
                ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
            );

            if( $character_response->getStatusCode() == 200 ) {

                DB::table('activity_modifiers')->where('type', 'nightmare_hunt')->delete(); // cleanup db

                $character = json_decode($character_response->getBody()->getContents());
                $character = collect($character);

                $nightmareHuntHashes = [
                    '571058904'  => 'Omnigul',
                    '1188363426' => 'Zydron, Gate Lord',
                    '1342492675' => 'Phogoth',
                    '1907493625' => 'Skolas, Kell of Kells',
                    '2450170731' => 'Crota, Son of Oryx',
                    '2639701103' => 'Fanatic',
                    '3205253945' => 'Taniks, the Scarred.',
                    '4098556693' => 'Dominus Ghaul'
                ];

                foreach($nightmareHuntHashes as $hash => $name) {

                    if( isset($character['Response']) ) {

                        $search = collect($character['Response']->activities->data->availableActivities)->filter(function($v) use ($hash) {
                            return $v->activityHash == $hash;
                        });

                        if( $search->count() > 0 ) {

                            $am = new App\Classes\Activity_Modifier();
                            $am->type = 'nightmare_hunt';
                            $am->hash = $hash;
                            $am->description = $activity_definitions[$hash]->displayProperties->description;
                            $am->name = $nightmareHuntHashes[$hash];
                            $am->icon = '/common/destiny2_content/icons/58bf5b93ae8cfefc55852fe664179757.png';
                            $am->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                            $am->save();

                            $this->info('Inserted NM Hunt: ' . $am->name);
                        }
                    }
                }
            }

            // Refresh Cache
            Cache::forget('milestones_activity_modifier');
            Cache::forever('milestones_activity_modifier', App\Classes\Activity_Modifier::get());
        }

        $this->info('Completed: milestones update');
    }
}
