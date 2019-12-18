<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;
use Cache;

class UpdateMemberExotic extends Command
{
    private $exoticHashes = [
        'weapons' => [
            'kinetic' => [
                1501322721, // monte carlo
                4207100358, // bad juju
                564802912,  // crimson
                564802914,  // huckleberry
                24541428,   // izanagi
                564802915,  // jade rabbit
                564802913,  // mida multi tool
                2500286745, // outbreak
                564802918,  // rat king
                564802916,  // sturm
                564802925,  // suros
                564802917,  // sweet business
                564802919,  // vigilance
                564802924,  // cerberus
                1660030044, // wish-ender
                1660030046, // ace of spades
                2924632392, // lumina
                1660030045, // malfeasance
                3074058273, // last word
                4009683574, // thorn
                2036397919, // arbalest
                1660030047  // chaperone
            ],
            'energy' => [
                1988948484, // divinity
                1642951317, // borealis
                1657028070, // coldheart
                2741465947, // eriana's vow
                1657028071, // fighting lion
                1657028069, // graviton
                1657028064, // hard light
                1657028078, // merciless
                1642951318, // polars
                1642951316, // prometheus
                1657028067, // riskrunner
                1657028066, // skyburner
                1657028068, // sunshot
                1642951319, // telesto
                2329697053, // tarrabah
                3573051804, // le monarque
                1642951312, // trinity ghoul
                3584311877, // jotuun
                1642951314, // lord of wolves
                47014674,   // Symmetry
                2190071629  // Devil's Ruin
            ],
            'power' => [
                888224289,  // deathbringer
                199171388,  // colony
                3695595899, // darci
                199171389,  // acrius
                199171391,  // prospector
                199171386,  // sleeper
                2094776121, // tractor
                199171390,  // wardcliff
                3875807583, // whisper
                199171387,  // worldline
                199171385,  // 1k voice
                2220014607, // anarchy
                199171382,  // queenbreaker
                3249389111, // thunderlord
                1763840761, // truth
                199171384,  // 2 tailed
                199171383,  // black talon
                1258579677, // Xenophage
                3552855013  // Leviathan's Breath
            ]
        ],
        'armor' => [
            'warlock' => [
                1902498454, // Stormdancer's Brace
                846189249,  // Apotheosis Veil
                4659035,    // Astrocyte Verse
                846189254,  // Crown of Tempests
                846189253,  // Eye of Another World
                846189252,  // Nezarec's Sin
                846189255,  // Skull of Dire Ahamkara
                846189251,  // The Stag
                846189250,  // Verity's Brow
                3087858765, // Aeon Soul
                3087858763, // Claws of Ahamkara
                3087858760, // Contraverse Hold
                3746353540, // Getaway Artist
                3087858767, // Karnstein Armlets
                3087858762, // Ophidian Aspect
                3087858766, // Sunbracers
                3087858764, // Winter's Guile
                860077158,  // Chromatic Fire
                860077159,  // Phoenix Protocol
                860077153,  // Sanguine Alchemy
                860077154,  // Starfire Protocol
                860077152,  // Vesper of Radius
                860077155,  // Wings of Sacred Dawn
                875969610,  // Geomag Stabilizers
                875969609,  // Lunafaction Boots
                875969608,  // Transversive Steps
                875969611   // Promethium Spur
            ],
            'hunter' => [
                899828456,  // Assassin's Cowl
                1234605995, // Celestial Nighthawk
                1234605992, // Foetracer
                1234605993, // Graviton Forfeit
                1234605994, // Knucklehead Radar
                1234605998, // Wormhusk Crown
                1240167153, // Aeon Swift
                1173508384, // Khepri's Sting
                2298909201, // Liar's Handshake
                1240167154, // Mechaneer's Tricksleeves
                1240167157, // Oathkeeper
                1240167159, // Sealed Ahamkara Grasps
                1240167158, // Shards of Galanor
                1240167152, // Shinobu's Vow
                1240167155, // Young Ahamkara's Spine
                3035639832, // Gwisin Vest
                3035639836, // Lucky Raspberry
                3035639838, // Ophidia Spathe
                3035639837, // Raiden Flux
                3035639839, // The Dragon's Shadow
                3035639833, // The Sixth Coyote
                975121089,  // Fr0st-EE5
                975121094,  // Gemini Jester
                975121093,  // Lucky Pants
                975121092,  // Orpheus Rig
                975121095,  // St0mp-EE5
                975121088   // The Bombardiers
            ],
            'titan' => [
                3073431847, // PHOENIX CRADLE
                2115530086, // An Insurmountable Skullfort
                2115530082, // Eternal Warrior
                2115530085, // Helm of Saint-14
                2115530084, // Khepri's Horn
                2115530087, // Mask of the Quiet One
                2115530083, // One-Eyed Mask
                1087913271, // ACD/0 Feedback Fence
                1087913268, // Aeon Safe
                1087913267, // Ashen Wake
                1087913270, // Doom Fang Pauldron
                1329699549, // Stronghold
                1087913269, // Synthoceps
                1087913265, // Ursa Furiosa
                1087913266, // Wormgod Caress
                3934937984, // Actium War Rig
                3934937986, // Armamentarium
                3934937985, // Crest of Alpha Lupi
                3934937987, // Hallowfire Heart
                3934937989, // Heart of Inmost Light
                2471990493, // Antaeus Wards
                2471990491, // Dunemarchers
                2471990489, // Lion Rampant
                2471990490, // Mk. 44 Stand Asides
                2471990488, // Peacekeepers
                657854178,  // Peregrine Greaves
                3934937988  // Severance Enclosure
            ],
        ]
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:memberExotic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update members\' exotic collections';

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
        $this->info('Begin: member collection update');

        $item_definitions = json_decode(file_get_contents(storage_path('manifest/DestinyCollectibleDefinition.json')));

        $client = new Client(['http_errors' => false, 'verify' => false]); //GuzzleHttp\Client

        $char_ids = [];

        $members = App\Classes\Clan_Member::get_members();
        $members = collect(json_decode($members));

        if( $members->count() > 0 ) {

            $n = 1;

            foreach($members as $member) {

                $this->info('Processing '.$n.' of '.count($members).' members: ' . $member->destinyUserInfo->displayName);
                $n++;

                $member_profile_response = $client->get(
                    env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.$member->destinyUserInfo->membershipId.'/?components=800',
                    ['headers' => ['X-API-Key' => env('BUNGIE_API')]]
                );

                if( $member_profile_response->getStatusCode() == 200 ) {
                    $member_profile = json_decode($member_profile_response->getBody()->getContents());
                    $member_profile = collect($member_profile);

                    foreach($this->exoticHashes['weapons'] as $type => $ids) {
                        foreach($ids as $id) {
                            if( isset($item_definitions->$id) ) {
                                $exotic_weapon = App\Classes\Exotic::updateOrCreate(
                                    ['id' => $id],
                                    [
                                        'name' => $item_definitions->$id->displayProperties->name,
                                        'description' => $item_definitions->$id->displayProperties->description,
                                        'icon' => $item_definitions->$id->displayProperties->hasIcon == true ? $item_definitions->$id->displayProperties->icon : '',
                                        'date_added' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
                                    ]
                                );
                            }

                            if( isset($member_profile['Response']->profileCollectibles->data->collectibles) ) {

                                $collection_records = collect($member_profile['Response']->profileCollectibles->data->collectibles);

                                if( isset($collection_records[$id]) ) {
                                    $is_collected = in_array($collection_records[$id]->state, [0, 8, 16, 32, 64]) ? 1 : 0;

                                    DB::table('clan_member_exotic_weapons')
                                    ->updateOrInsert(
                                        ['user_id' => $member->destinyUserInfo->membershipId, 'item_hash' => $id],
                                        ['is_collected' => $is_collected, 'date_added' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]
                                    );
                                }
                            }
                            else {
                                $this->info('Unable to retrieve collection data.');
                            }
                        }
                    }

                    foreach($this->exoticHashes['armor'] as $class => $ids) {
                        foreach($ids as $id) {
                            if( isset($item_definitions->$id) ) {

                                $exotic_weapon = App\Classes\Exotic::updateOrCreate(
                                    ['id' => $id],
                                    [
                                        'name' => $item_definitions->$id->displayProperties->name,
                                        'description' => $item_definitions->$id->displayProperties->description,
                                        'icon' => $item_definitions->$id->displayProperties->hasIcon == true ? $item_definitions->$id->displayProperties->icon : '',
                                        'date_added' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
                                    ]
                                );
                            }

                            if( isset($member_profile['Response']->characterCollectibles->data) ) {
                                $chars = collect($member_profile['Response']->characterCollectibles->data);

                                foreach($chars as $char) {
                                    $collection_records = collect($char->collectibles);

                                    if( isset($collection_records[$id]->state) ) {
                                        $is_collected = in_array($collection_records[$id]->state, [0, 8, 16, 32, 64]) ? 1 : 0;

                                        DB::table('clan_member_exotic_armors')
                                        ->updateOrInsert(
                                            ['user_id' => $member->destinyUserInfo->membershipId, 'item_hash' => $id, 'class' => $class],
                                            ['is_collected' => $is_collected, 'date_added' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]
                                        );

                                        if( $is_collected == 1 ) break;
                                    }
                                }
                            }
                            else {
                                $this->info('Unable to retrieve collection data.');
                            }
                        }
                    }
                }
            }
        }

        // Refresh Cache
        Cache::forget('clan_exotic_weapon_collection');
        Cache::forever('clan_exotic_weapon_collection', DB::table("clan_member_exotic_weapons")->get());

        Cache::forget('clan_exotic_armor_collection');
        Cache::forever('clan_exotic_armor_collection', DB::table("clan_member_exotic_armors")->get());

        Cache::forget('exotic_definition');
        Cache::forever('exotic_definition', DB::table("exotics")->get());

        $this->info('Completed: member collection update');
    }
}
