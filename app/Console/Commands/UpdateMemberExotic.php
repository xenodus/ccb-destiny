<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;

class UpdateMemberExotic extends Command
{
    private $exoticWeaponHashes = [
        'weapons' => [
            'kinetic' => [
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
                1642951317, // borealis
                1657028070, // coldheart
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
                1642951314  // lord of wolves
            ],
            'power' => [
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
                199171383   // black talon
            ]
        ],
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

        $client = new Client(['http_errors' => false, 'verify' => false]); //GuzzleHttp\Client
        $member_response = $client->get(
          str_replace('https://', 'http://', route('bungie_get_members'))
        );

        $char_ids = [];

        if( $member_response->getStatusCode() == 200 ) {
            $members = json_decode($member_response->getBody()->getContents());
            $members = collect($members);

            $n = 1;

            foreach($members as $member) {

                $this->info('Processing '.$n.' of '.count($members).' members');
                $n++;

                $member_profile_response = $client->get(
                    env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.$member->destinyUserInfo->membershipId.'/?components=800',
                    ['headers' => ['X-API-Key' => env('BUNGIE_API')]]
                );

                if( $member_profile_response->getStatusCode() == 200 ) {
                    $member_profile = json_decode($member_profile_response->getBody()->getContents());
                    $member_profile = collect($member_profile);

                    foreach($this->exoticWeaponHashes['weapons'] as $type => $ids) {
                        foreach($ids as $id) {

                            if( isset($member_profile['Response']->profileCollectibles->data->collectibles) ) {

                                $collection_records = collect($member_profile['Response']->profileCollectibles->data->collectibles);

                                if( isset($collection_records[$id]) ) {
                                    $is_collected = $collection_records[$id]->state == 1 ? 0 : 1;

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
                }
            }
        }

        $this->info('Completed: member collection update');
    }
}
