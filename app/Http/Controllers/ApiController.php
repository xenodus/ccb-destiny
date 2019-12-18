<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use App;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Goutte;
use Carbon\Carbon;
use Cookie;
use Illuminate\Http\Request;
use Cache;

class ApiController extends Controller
{
    // values = key words to search in news api
    const NEWS_CATEGORIES = [
        'destiny' => 'Destiny 2 Bungie',
        'division' => 'Ubisoft Division',
        'magic' => 'mtg arena magic'
    ];

    const HIDE_ACTIVITY = true;

    function get_clan_applications(Request $request) {

        if( $request->input('api-key') == env('SITE_CCB_BOT_KEY') ) {

            $applications = App\Classes\Application::where('bot_processed', 0)->get();

            if( $request->input('ids') ) {
                $ids = explode( ',', $request->input('ids') );
                App\Classes\Application::whereIn('id', $ids)
                ->update(['bot_processed' => 1]);
            }

            return response()->json($applications);
        }

        return response()->json([]);
    }

    function get_milestones() {
        $activity_modifiers = Cache::rememberForever('milestones_activity_modifier', function () {
            return App\Classes\Activity_Modifier::get();
        });

        $nightfalls = Cache::rememberForever('milestones_nightfall', function () {
            return App\Classes\Nightfall::get();
        });

        $vendor_sales = Cache::rememberForever('vendor_sales', function () {
            return App\Classes\Vendor_Sales::with('costs')->orderBy('vendor_hash')->get();
        });

        // Xur Items' Perks
        $xur_sales_item_perks = Cache::rememberForever('vendor_sales_item_perks_xur', function () {
            return App\Classes\Vendor_Sales_Item_Perks::whereHas('vendor_sales', function($q) {
                $q->where('vendor_hash', '2190858386');
            })->get();
        });

        $milestones = [
            'milestones' => $activity_modifiers,
            'nightfalls' => $nightfalls,
            'vendor_sales' => $vendor_sales,
            'xur_sales_item_perks' => $xur_sales_item_perks,
            'escalation_protocol' => $this->get_escalation_protocol(),
            'ascendant_challenge' => $this->get_ascendant_challenge(),
            'dreaming_city_curse_level' => $this->get_dc_curse_level(),
            'dreaming_city_mission' => $this->get_dc_mission(),
            'whisper_singe' => $this->get_whisper_singe(),
            'outbreak_singe' => $this->get_outbreak_singe(),
            'reckoning' => $this->get_reckoning(),
            'gos_challenge' => $this->get_gos_challenge(),
            'altar_of_sorrows' => $this->get_altar_of_sorrows()
        ];

        return response()->json($milestones);
    }

    public static function get_escalation_protocol() {

      $epRotations = [
        [
          'boss' => 'Kathok, Roar of Xol',
          'name' => 'IKELOS_SMG_v1.0.1',
          'icon' => '/common/destiny2_content/icons/85ad82abdfc13537325b45a85d6f4462.jpg'
        ],
        [
          'boss' => 'Damkath, The Mask',
          'name' => 'IKELOS_SR_v1.0.1',
          'icon' => '/common/destiny2_content/icons/52630df015ef0e839555982c478d78f3.jpg'
        ],
        [
          'boss' => 'Naksud, the Famine',
          'name' => 'All 3 Weapons',
          'icon' => '/common/destiny2_content/icons/d316fa414f16795f5f0674a35d2bdae7.jpg'
        ],
        [
          'boss' => 'Bok Litur, Hunger of Xol',
          'name' => 'All 3 Weapons',
          'icon' => '/common/destiny2_content/icons/d316fa414f16795f5f0674a35d2bdae7.jpg'
        ],
        [
          'boss' => 'Nur Abath, Crest of Xol',
          'name' => 'IKELOS_SG_v1.0.1',
          'icon' => '/common/destiny2_content/icons/edfdd807c9d604e80b48ad8fe39c8f36.jpg'
        ]
      ];

      $startDate = Carbon::create(2019, 1, 16, 1, 0, 0);
      $currDate = Carbon::now();

      $index = 0;
      $found = false;

      while($found == false) {

        // Reset rotations
        if( $index == count($epRotations) ) {
          $index = 0;
        }

        $nextWeek = $startDate->copy()->addDays(7);

        if( $currDate->between($startDate, $nextWeek) ) {
          $found = true;
        }
        else {
          $startDate = $nextWeek;
          $index++;
        }
      }

      return [
        'name' => $epRotations[$index]['name'],
        'icon' => $epRotations[$index]['icon'],
        'description' => 'Boss: ' . $epRotations[$index]['boss']
      ];
    }

    public static function get_ascendant_challenge() {

      $acRotations = [
        [
          'name' => 'Gardens of Esila',
          'description' => 'At the overlook\'s edge, the garden grows onward.'
        ],
        [
          'name' => 'Spine of Keres',
          'description' => 'Climb the bones and you\'ll find your ruin.'
        ],
        [
          'name' => 'Harbinger’s Seclude',
          'description' => 'Crush the first queen\'s crown beneath your bootheel.'
        ],
        [
          'name' => 'Bay of Drowned Wishes',
          'description' => 'Drown in your wishes, dear squanderer.'
        ],
        [
          'name' => 'Chamber of Starlight',
          'description' => 'Starlight, star bright, first untruth she\'ll craft tonight...'
        ],
        [
          'name' => 'Aphelion’s Rest',
          'description' => 'They call it a \'rest\', but it is more truly a haunt.'
        ],
      ];

      $startDate = Carbon::create(2019, 1, 16, 1, 0, 0);
      $currDate = Carbon::now();

      $index = 0;
      $found = false;

      while($found == false) {

        // Reset rotations
        if( $index == count($acRotations) ) {
          $index = 0;
        }

        $nextWeek = $startDate->copy()->addDays(7);

        if( $currDate->between($startDate, $nextWeek) ) {
          $found = true;
        }
        else {
          $startDate = $nextWeek;
          $index++;
        }
      }

      return [
        'name' => 'Ascendant: ' . $acRotations[$index]['name'],
        'icon' => '/common/destiny2_content/icons/2f9e7dd03c415eb158c16bb59cc24c84.jpg',
        'description' => $acRotations[$index]['description']
      ];
    }

    public static function get_dc_mission() {

      $dcMissionRotations = [
        [
          'name' => 'Broken Courier',
          'description' => 'Respond to a distress call in the Strand.'
        ],
        [
          'name' => 'Oracle Engine',
          'description' => 'The Taken threaten to take control of an irreplaceable Awoken communications device.'
        ],
        [
          'name' => 'Dark Monastery',
          'description' => 'Provide recon for Petra\'s forces by investigating strange enemy activity in Rheasilvia.'
        ]
      ];

      $startDate = Carbon::create(2019, 1, 16, 1, 0, 0);
      $currDate = Carbon::now();

      $index = 0;
      $found = false;

      while($found == false) {

        // Reset rotations
        if( $index == count($dcMissionRotations) ) {
          $index = 0;
        }

        $nextWeek = $startDate->copy()->addDays(7);

        if( $currDate->between($startDate, $nextWeek) ) {
          $found = true;
        }
        else {
          $startDate = $nextWeek;
          $index++;
        }
      }

      return [
        'name' => 'Mission: ' . $dcMissionRotations[$index]['name'],
        'icon' => '/common/destiny2_content/icons/43541be45952e7eec59b7b57a0bf15a3.png',
        'description' => $dcMissionRotations[$index]['description']
      ];
    }

    public static function get_dc_curse_level() {

      $curseLevel = [
          'Low',
          'Medium',
          'High'
      ];

      $startDate = Carbon::create(2019, 1, 16, 1, 0, 0);
      $currDate = Carbon::now();

      $index = 0;
      $found = false;

      while($found == false) {

        // Reset rotations
        if( $index == count($curseLevel) ) {
          $index = 0;
        }

        $nextWeek = $startDate->copy()->addDays(7);

        if( $currDate->between($startDate, $nextWeek) ) {
          $found = true;
        }
        else {
          $startDate = $nextWeek;
          $index++;
        }
      }

      $description = 'The curse level is <u>'.strtolower($curseLevel[$index]).'</u> in the Dreaming City.';

      return [
        'name' => 'Curse Level: ' . $curseLevel[$index],
        'icon' => '/common/destiny2_content/icons/8f755eb3a9109ed7adfc4a8b27871e7a.png',
        'description' => $description
      ];
    }

    public static function get_whisper_singe() {

      $whisperSinges = [
        [
          'name' => 'Void',
          'icon' => '/common/destiny2_content/icons/150c14552f0138feadcc157571e0b0e6.png',
          'description' => 'Void damage increases slightly from all sources.'
        ],
        [
          'name' => 'Arc',
          'icon' => '/common/destiny2_content/icons/ee1536e4ab72c6286ab68980d1ce6ecb.png',
          'description' => 'Arc damage increases slightly from all sources.'
        ],
        [
          'name' => 'Solar',
          'icon' => '/common/destiny2_content/icons/608fb3a03d42f16f85788abe799b0af0.png',
          'description' => 'Solar damage increases slightly from all sources.'
        ],
      ];

      $startDate = Carbon::create(2019, 7, 31, 1, 0, 0);
      $currDate = Carbon::now();

      $index = 0;
      $found = false;

      while($found == false) {

        // Reset rotations
        if( $index == count($whisperSinges) ) {
          $index = 0;
        }

        $nextWeek = $startDate->copy()->addDays(7);

        if( $currDate->between($startDate, $nextWeek) ) {
          $found = true;
        }
        else {
          $startDate = $nextWeek;
          $index++;
        }
      }

      return [
        'name' => $whisperSinges[$index]['name'] . ' Singe',
        'icon' => '/common/destiny2_content/icons/b760b737519af909e26f21009d6a1487.jpg',
        'description' => $whisperSinges[$index]['description']
      ];
    }

    public static function get_outbreak_singe() {

      $outbreakSinges = [
        'Void',
        'Arc',
        'Solar'
      ];

      $startDate = Carbon::create(2019, 5, 8, 1, 0, 0);
      $currDate = Carbon::now();

      $index = 0;
      $found = false;

      while($found == false) {

        // Reset rotations
        if( $index == count($outbreakSinges) ) {
          $index = 0;
        }

        $nextWeek = $startDate->copy()->addDays(7);

        if( $currDate->between($startDate, $nextWeek) ) {
          $found = true;
        }
        else {
          $startDate = $nextWeek;
          $index++;
        }
      }

      return [
        'name' => $outbreakSinges[$index] . ' Configuration',
        'icon' => '/common/destiny2_content/icons/c013e41cdb32779bc2322337614ea06b.jpg',
        'description' => 'The configuration type is <u class="color-'.strtolower($outbreakSinges[$index]).'">'.strtolower($outbreakSinges[$index]).'</u> for Zero Hour (Heroic).'
      ];
    }

    public static function get_reckoning() {

      $bosses = [
        'Sword Knights',
        'Likeness of Oryx'
      ];

      $startDate = Carbon::create(2019, 5, 29, 1, 0, 0);
      $currDate = Carbon::now();

      $index = 0;
      $found = false;

      while($found == false) {

        // Reset rotations
        if( $index == count($bosses) ) {
          $index = 0;
        }

        $nextWeek = $startDate->copy()->addDays(7);

        if( $currDate->between($startDate, $nextWeek) ) {
          $found = true;
        }
        else {
          $startDate = $nextWeek;
          $index++;
        }
      }

      if( $index == 0 )
        $description = 'The bosses for this week\'s reckoning activity are the <u>' . $bosses[$index] . '</u>.';
      else
        $description = 'The boss for this week\'s reckoning activity is the <u>' . $bosses[$index] . '</u>.';

      $data[] = [
        'name' => 'Tier 2/3 Boss: ' . $bosses[$index],
        'icon' => '/common/destiny2_content/icons/fc31e8ede7cc15908d6e2dfac25d78ff.png',
        'description' => $description
      ];

      if( $index == 0 ) {
        $data[] = [
            [
              "name" => "Lonesome",
              "description" => "Kinetic Sidearm<br/><br/>Am I the only one who sees?",
              "icon" => "/common/destiny2_content/icons/abd91ac904ddb37308898c9a5fd38b02.jpg",
            ],
            [
              "name" => "Night Watch",
              "description" => "Kinetic Scout Rifle<br/><br/>Sleep with both eyes open.",
              "icon" => "/common/destiny2_content/icons/f32f6b8896ca5b2684c6e02d447f5182.jpg",
            ],
            [
              "name" => "Sole Survivor",
              "description" => "<span class='color-arc'>Arc</span> Sniper Rifle<br/><br/>Names mean nothing to the dead.",
              "icon" => "/common/destiny2_content/icons/0ae824a841009f28327d905c0610b03c.jpg",
            ],
            [
              "name" => "Last Man Standing",
              "description" => "<span class='color-solar'>Solar</span> Shotgun<br/><br/>Call me Ozymandias.",
              "icon" => "/common/destiny2_content/icons/d39006fe5498ec8720622da5a31dd066.jpg",
            ],
            [
              "name" => "Just in Case (Tier 3 Only)",
              "description" => "<span class='color-solar'>Solar</span> Sword<br/><br/>Even contingencies need contingencies.",
              "icon" => "/common/destiny2_content/icons/c32e9275a505a1e39bfc146dca3702b6.jpg",
            ]
        ];
      }
      else {
        $data[] = [
            [
              "name" => "Spare Rations",
              "description" => "Kinetic Hand Cannon<br/><br/>Whether times are lean or fat.",
              "icon" => "/common/destiny2_content/icons/7106d949c81a1b2b281964ae2184d6b2.jpg",
            ],
            [
              "name" => "Bug-Out Bag",
              "description" => "<span class='color-solar'>Solar</span> SMG<br/><br/>Grab and go.",
              "icon" => "/common/destiny2_content/icons/870aa58f8314ca60ec3075f937735885.jpg",
            ],
            [
              "name" => "Outlast",
              "description" => "<span class='color-solar'>Solar</span> Pulse Rifle<br/><br/>No such word as extinction.",
              "icon" => "/common/destiny2_content/icons/7967ce5273a19ca50fe3ec1fd1b1b375.jpg",
            ],
            [
              "name" => "Gnawing Hunger",
              "description" => "<span class='color-void'>Void</span> Auto Rifle<br/><br/>Don't let pride keep you from a good meal.",
              "icon" => "/common/destiny2_content/icons/48037e6416c3c9da07030a72931e0ca9.jpg",
            ],
            [
              "name" => "Doomsday (Tier 3 Only)",
              "description" => "<span class='color-arc'>Arc</span> Grenade Launcher<br/><br/>The age-old chant: The end of days draws nigh.",
              "icon" => "/common/destiny2_content/icons/f689eb2328e786599701352b9c01b64d.jpg",
            ],
        ];
      }

      return $data;
    }

    public static function get_gos_challenge() {

      $challenges = [
        [
          'name' => 'Zero to One Hundred',
          'description' => 'Encounter 4: Bank 30 motes within 10s for each relay. 3 guardians with 10 motes each bank one after another.'
        ],
        [
          'name' => 'Staying Alive',
          'description' => 'Encounter 1: Leave double Cyclops spawns alive.'
        ],
        [
          'name' => 'A Link on The Chain',
          'description' => 'Encounter 2: Everyone tether within 5s of each other.'
        ],
        [
          'name' => 'To The Top',
          'description' => 'Encounter 3: Each guardian must only bank 10 motes.'
        ],
      ];

      $startDate = Carbon::create(2019, 11, 27, 1, 0, 0);
      $currDate = Carbon::now();

      $index = 0;
      $found = false;

      while($found == false) {

        // Reset rotations
        if( $index == count($challenges) ) {
          $index = 0;
        }

        $nextWeek = $startDate->copy()->addDays(7);

        if( $currDate->between($startDate, $nextWeek) ) {
          $found = true;
        }
        else {
          $startDate = $nextWeek;
          $index++;
        }
      }

      return [
        'name' => $challenges[$index]['name'],
        'icon' => '/common/destiny2_content/icons/6c13fd357e95348a3ab1892fc22ba3ac.png',
        'description' => $challenges[$index]['description']
      ];
    }

    public static function get_altar_of_sorrows() {

      $altarRotations = [
        [
          'name' => 'Apostate',
          'icon' => '/common/destiny2_content/icons/b990412136d220fd641078418a4903fe.jpg',
          'description' => '<span class=\'color-arc\'>Arc</span> Sniper Rifle<br/><br/>"Survival is our most holy writ. Heterodoxy will be its own undoing." — Kuldax',
          'boss' => 'Nightmare of Taniks (Fallen Captain)'
        ],
        [
          'name' => 'Heretic',
          'icon' => '/common/destiny2_content/icons/eaf113dbb5cea03526009e6030b8c8ee.jpg',
          'description' => '<span class=\'color-arc\'>Arc</span> Rocker Launcher<br/><br/>"Death is only and forever an ending. All else is sacrilege." — Kuldax',
          'boss' => 'Nightmare of Zydron (Vex Minotaur)'
        ],
        [
          'name' => 'Blasphemer',
          'icon' => '/common/destiny2_content/icons/2f61559b7c57894703b6aaa52a44630c.jpg',
          'description' => 'Kinetic Shotgun<br/><br/>"The logic is ineluctable: Those who die deserve oblivion." — Kuldax',
          'boss' => 'Nightmare of Phogoth (Hive Ogre)'
        ],
      ];

      $startDate = Carbon::create(2019, 11, 18, 1, 0, 0);
      $currDate = Carbon::now();

      $index = 0;
      $found = false;

      while($found == false) {

        // Reset rotations
        if( $index == count($altarRotations) ) {
          $index = 0;
        }

        $nextWeek = $startDate->copy()->addDays(7);

        if( $currDate->between($startDate, $nextWeek) ) {
          $found = true;
        }
        else {
          $startDate = $nextWeek;
          $index++;
        }
      }

      $data[] = [
        'name' => $altarRotations[$index]['boss'],
        'icon' => '/common/destiny2_content/icons/58bf5b93ae8cfefc55852fe664179757.png',
        'description' => 'The final boss is ' . $altarRotations[$index]['boss']
      ];

      $data[] = $altarRotations[$index];

      return $data;
    }

    function refresh_cache() {

        // clan members
        Cache::forget('clan_members');
        Cache::forever('clan_members', App\Classes\Clan_Member::get());

        // clan members characters
        Cache::forget('clan_members_characters');
        Cache::forever('clan_members_characters', App\Classes\Clan_Member::with('characters')->with('platform_profile')->with('aliases')->get());

        // Stats
        Cache::forget('pve_stats');
        Cache::forever('pve_stats', App\Classes\Pve_Stats::get());
        Cache::forget('weapon_stats');
        Cache::forever('weapon_stats', App\Classes\Weapon_Stats::get());
        Cache::forget('pvp_stats');
        Cache::forever('pvp_stats', App\Classes\Pvp_Stats::get());
        Cache::forget('raid_stats');
        Cache::forever('raid_stats', App\Classes\Raid_Stats::get());
        Cache::forget('gambit_stats');
        Cache::forever('gambit_stats', App\Classes\Gambit_Stats::get());

        // Milestones
        Cache::forget('milestones_activity_modifier');
        Cache::forever('milestones_activity_modifier', App\Classes\Activity_Modifier::get());
        Cache::forget('milestones_nightfall');
        Cache::forever('milestones_nightfall', App\Classes\Nightfall::get());

        // Vendors
        $vendor_sales_item_perks_xur = App\Classes\Vendor_Sales_Item_Perks::whereHas('vendor_sales', function($q) {
            $q->where('vendor_hash', '2190858386');
        })->get();

        Cache::forget('vendor_sales_item_perks_xur');
        // Cache::forever('vendor_sales_item_perks_xur', $vendor_sales_item_perks_xur);
        Cache::forget('vendor_sales');
        // Cache::forever('vendor_sales', App\Classes\Vendor_Sales::orderBy('vendor_hash')->get());
        Cache::forget('vendor_sales_item_perks');
        // Cache::forever('vendor_sales_item_perks', App\Classes\Vendor_Sales_Item_Perks::get());

        // Raid Lockouts
        Cache::forget('clan_raid_lockouts');
        Cache::forever('clan_raid_lockouts', App\Classes\Raid_Lockouts::get());

        // Seals
        Cache::forget('clan_seal_completions');
        Cache::forever('clan_seal_completions', App\Classes\Seal_Completions::get());

        // Exotic Collection
        Cache::forget('clan_exotic_weapon_collection');
        Cache::forever('clan_exotic_weapon_collection', DB::table("clan_member_exotic_weapons")->get());

        Cache::forget('clan_exotic_armor_collection');
        Cache::forever('clan_exotic_armor_collection', DB::table("clan_member_exotic_armors")->get());

        Cache::forget('exotic_definition');
        Cache::forever('exotic_definition', DB::table("exotics")->get());

        // Clan Activity
        Cache::forget('clan_activity');

        Cache::forget('clan_member_raid_buddies');
        Cache::forget('clan_pvp_buddy');

        // Home Page Stats
        Cache::forget('home_raids_completed');
        Cache::forget('home_pve_kills');
        Cache::forget('clan_members');

        return response()->json(1);
    }

    function get_record_definition() {
        $def = file_get_contents( storage_path('manifest/DestinyRecordDefinition.json') );
        return response()->json(json_decode($def, true));
    }

    function get_membership_id_from_bnet_id($bnet_id) {

        $client = new Client(); //GuzzleHttp\Client
        $response = $client->get(
            env('BUNGIE_API_ROOT_URL').'/Destiny2/SearchDestinyPlayer/'.env('BUNGIE_PC_PLATFORM_ID').'/'.urlencode($bnet_id).'/',
            [
                'headers' => ['X-API-Key' => env('BUNGIE_API')],
                'http_errors' => false
            ]
        );

        if( $response->getStatusCode() == 200 ) {

            $data = json_decode($response->getBody()->getContents());
            $data = collect($data);

            if( count($data['Response']) > 0 ) {
                return response()->json($data['Response'][0]);
            }
        }

        return response()->json([]);
    }

    function update_glory_from_db(Request $request) {

        $glory_hash = 2000925172;
        $names = explode(',', $request->input('names'));
        $results = [];

        foreach($names as $name) {
            $member = App\Classes\Clan_Member::where('display_name', $name)->first();

            if( $member ) {

                $result = [
                    'name' => $name,
                    'glory' => 0
                ];

                $client = new Client(); //GuzzleHttp\Client

                $member_profile_response = $client->get(
                    env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.$member->id.'?components=100,202,900',
                    ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
                );

                if( $member_profile_response->getStatusCode() == 200 ) {
                    $member_profile = json_decode($member_profile_response->getBody()->getContents());
                    $member_profile = collect($member_profile);

                    $character_id =  key(collect($member_profile['Response']->characterProgressions->data)->toArray());

                    $result['glory'] = $member_profile['Response']->characterProgressions->data->$character_id->progressions->$glory_hash->currentProgress;
                }

                $results[] = $result;
            }
        }

        return response()->json($results);
    }

    function closest_glory(Request $request) {

        $points = explode(',', $request->input('points'));
        $names = explode(',', $request->input('names'));

        if( count($points) && count($names) ) {

            $results = [];
            $smallest_index = -1;

            $combinations = new \drupol\phpermutations\Generators\Combinations($points, 4);
            $combinations = $combinations->toArray();

            // Just Brute Force
            foreach($combinations as $c_key => $c) {
                $team1 = $c;
                $team2 = $points;

                foreach($team1 as $p1) {
                    foreach($team2 as $key => $p2) {
                        if( $p1 == $p2 ) {
                            unset($team2[$key]);
                            break;
                        }
                    }
                }

                $diff = array_sum($team1) - array_sum($team2) > 0 ? array_sum($team1) - array_sum($team2) : abs(array_sum($team1) - array_sum($team2));

                $results[] = [
                    'team1' => $team1,
                    'team2' => array_values($team2),
                    'diff' => $diff,
                    'team1_total' => array_sum($team1),
                    'team2_total' => array_sum($team2),
                ];

                if( $smallest_index == -1 ) {
                    $smallest_index = 0;
                    $smallest_diff = $results[$smallest_index]['diff'];
                }
                elseif( $diff < $smallest_diff ) {
                    $smallest_index = $c_key;
                    $smallest_diff = $diff;
                }
            }

            return response()->json($results[$smallest_index])
                ->cookie(Cookie::forever('glory_names', $request->input('names')))
                ->cookie(Cookie::forever('glory_points', $request->input('points')));
        }

        return response()->json([]);
    }

    function get_mtg_top_decks() {
        $decks = [];
        $client = new Goutte\Client();
        $crawler = $client->request('GET', 'https://www.mtggoldfish.com/metagame/standard#paper');

        if( $crawler->filter('table.table.table-condensed.sample_deck')->count() ) {
            $t = $crawler->filter('table.sample_deck')->each(function($node) use (&$decks) {
                $deck_set['event_name'] = str_replace("\n", " ", trim($node->previousAll()->filter('h3')->first()->text()));
                $deck_set['event_link'] = $node->previousAll()->filter('h3 a')->first()->link()->getUri();

                $node->filter('td.col-deck > span.deck-price-paper')->each(function ($node) use (&$deck_set) {
                    $html = trim(str_replace('<a href="/deck/', '<a target="_blank" href="https://www.mtggoldfish.com/deck/', $node->html()));

                    $deck_set['decks'][] = [
                        'link' => $node->filter('a')->first()->link()->getUri(),
                        'name' => trim($node->text())
                    ];
                });

                if( isset($deck_set['decks']) && count($deck_set['decks']) ) $decks[] = $deck_set;
            });
        }

        return response()->json($decks);
    }

    function get_news($category='') {

        // All news
        $cache_time = 15 * 60;
        $all_news = Cache::remember('all_news', $cache_time, function () {
            return App\Classes\News_Feed::where('status', 'active')->get();
        });

        if( in_array($category, array_keys(self::NEWS_CATEGORIES)) ) {
            $news = $all_news->where('category', $category)->take(5);
        }
        else {
            $destiny_news = $all_news->where('status', 'active')->where('category', 'destiny')->take(5);
            $division_news = $all_news->where('status', 'active')->where('category', 'division')->take(5);
            $magic_news = $all_news->where('status', 'active')->where('category', 'magic')->take(5);
            $news = $destiny_news->merge($division_news)->merge($magic_news);
        }

        return response()->json($news);
    }

    function update_news() {
        $topics = self::NEWS_CATEGORIES;

        foreach($topics as $key => $topic) {
            // top-headlines | everything
            // category: business entertainment general health science sports technology
            // language: ar de en es fr he it nl no pt ru se ud zh
            $url = 'https://newsapi.org/v2/everything?q='.urlencode($topic).'&language=en&sortBy=publishedAt&sortBy=popularity&sortBy=relevancy&apiKey='.env('NEWS_API');

            $client = new Client(); //GuzzleHttp\Client
            $news_response = $client->get($url);

            if( $news_response->getStatusCode() == 200 ) {
                $news = json_decode($news_response->getBody()->getContents());
                $news = collect($news);

                //dd($news);

                if( count($news['articles']) ) {

                    DB::table('news_feed')->where('category', $key)->update(['status' => 'expired']);
                    DB::table('news_feed')->where('category', $key)->whereRaw('date_added <= now() - INTERVAL 3 HOUR')->delete();

                    foreach($news['articles'] as $article) {

                        $n = App\Classes\News_Feed::where('status', 'active')->where(function($query) use ($article){
                          $query->where('url', $article->url)->orWhere('title', $article->title);
                        })->count();


                        if($n == 0 && self::is_english($article->title)) {
                            $news = new App\Classes\News_Feed;
                            $news->author = $article->author ?? '';
                            $news->category = $key;
                            $news->status = 'active';
                            $news->source = $article->source->name;
                            $news->date_added = Carbon::now()->format('Y-m-d H:i:s');
                            $news->title = $article->title;
                            $news->description = $article->description;
                            $news->url = $article->url;
                            $news->thumbnail = $article->urlToImage ?? '';
                            $news->date_published = Carbon::parse($article->publishedAt)->format('Y-m-d');
                            $news->save();
                        }
                    }
                }
            }
        }

        $news = App\Classes\News_Feed::where('status', 'active')->get();

        return response()->json($news);
    }

    public static function is_english($str)
    {
        if (strlen($str) != strlen(utf8_decode($str))) {
            return false;
        } else {
            return true;
        }
    }

    function get_xur_location() {
        $location = '';
        $client = new Goutte\Client();
        $crawler = $client->request('GET', 'https://wherethefuckisxur.com/');

        if( $crawler->filter('div.xur-location > h1')->count() ) {
            $location = $crawler->filter('div.xur-location > h1')->text();
        }

        return response()->json(['location' => $location]);
    }

    function get_vendor($vendor_id='') {

        if( $vendor_id )
            $vendor_sales = App\Classes\Vendor_Sales::where('vendor_hash', $vendor_id);
        else
            $vendor_sales = App\Classes\Vendor_Sales::orderBy('vendor_hash');

        if($vendor_id=='672118013')
            $vendor_sales = $vendor_sales->orderBy('cost_name');
        else
            $vendor_sales = $vendor_sales->orderBy('itemTypeDisplayName');

        $vendor_sales = $vendor_sales->get();

        return response()->json($vendor_sales);
    }

    function get_sales_item_perks($vendor_id) {

        $vendor_sales_item_perks = App\Classes\Vendor_Sales_Item_Perks::whereHas('vendor_sales', function($q) use($vendor_id){
            $q->where('vendor_hash', $vendor_id);
        })->get();

        return response()->json($vendor_sales_item_perks);
    }

    public function activity()
    {
        $data = [];
        $cache_time = 60;
        $data = Cache::remember('clan_activity', $cache_time, function () use(&$data) {

            $client = new Client(); //GuzzleHttp\Client

            $activity_definitions = collect(json_decode(file_get_contents(storage_path('manifest/DestinyActivityDefinition.json'))));

            // 1. Get members online
            $members_online = App\Classes\Clan_Member::get_members_online();
            $members_online = collect(json_decode($members_online))->toArray();

            if( count($members_online) > 0 ) {
                // 2. Get members'
                foreach($members_online as $member) {

                    $membership_id = $member->membershipId;

                    if( in_array($membership_id, ['4611686018474971535', '4611686018472137936']) && self::HIDE_ACTIVITY == true ) continue;

                    $res = $client->get( env('BUNGIE_API_ROOT_URL').'/Destiny2/'.$member->membershipType.'/Profile/'.$membership_id.'?components=100,204', ['headers' => ['X-API-Key' => env('BUNGIE_API')]] );

                    if( $res->getStatusCode() == 200 ) {
                        $member_profile = collect(json_decode($res->getBody()->getContents()));

                        $member_activity = new App\Classes\Member_Activity();
                        $member_activity->displayName = $member_profile['Response']->profile->data->userInfo->displayName;
                        $member_activity->lastSeen = Carbon::parse($member_profile['Response']->profile->data->dateLastPlayed)->timezone('Asia/Singapore')->format('g:i A');

                        if( isset($member_profile['Response']->characterActivities->data) ) {
                            foreach( $member_profile['Response']->characterActivities->data as $character_id => $character_activity ) {
                                if( isset($latest_activity) ) {
                                    $prev_activity_dt = Carbon::parse($latest_activity->dateActivityStarted);
                                    $curr_activity = Carbon::parse($character_activity->dateActivityStarted);

                                    if( $curr_activity->greaterThan($prev_activity_dt) ) {
                                        $latest_activity = $character_activity;
                                        $member_activity->character_id = $character_id;
                                    }
                                }
                                else {
                                    $latest_activity = $character_activity;
                                    $member_activity->character_id = $character_id;
                                }
                            }

                            $member_activity->latestActivityTime = Carbon::parse($latest_activity->dateActivityStarted)->timezone('Asia/Singapore')->format('g:i A');

                            $member_activity->latestActivity = $activity_definitions->where('hash', $latest_activity->currentActivityHash)->first();

                            if( $member_activity->latestActivity ) {
                                // Orbit
                                if( $member_activity->latestActivity->placeHash == '2961497387' ) {
                                    $member_activity->latestActivity->originalDisplayProperties->name = "Orbit";
                                    $member_activity->latestActivity->displayProperties->name = "Orbit";
                                }

                                $data[] = $member_activity;
                            }

                            unset( $latest_activity );
                        }
                    }
                }
            }
            return $data;
        });

        return response()->json($data);
    }
}