<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;
use Cache;

class UpdateCharacters extends Command
{
    // characters
    private $class_hash = [
    671679327 => 'hunter',
    3655393761 => 'titan',
    2271682572 => 'warlock'
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:characters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update members\' characters';

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
        $client = new Client(['http_errors' => false, 'verify' => false]); //GuzzleHttp\Client

        $char_ids = [];

        $members = App\Classes\Clan_Member::get_members();
        $members = collect(json_decode($members));

        if( $members->count() > 0 ) {

            $n = 1;

            $this->info('Begin: Characters update');

            foreach($members as $member) {

                $this->info('Processing '.$n.' of '.count($members).': ' . $member->destinyUserInfo->displayName);
                $n++;

                $member_characters_response = $client->get(
                    env('BUNGIE_API_ROOT_URL').'/Destiny2/'.$member->destinyUserInfo->membershipType.'/Profile/'.$member->destinyUserInfo->membershipId.'?components=200,204',
                    [
                        'headers' => [
                            'X-API-Key' => env('BUNGIE_API')
                        ]
                    ]
                );

                if( $member_characters_response->getStatusCode() == 200 ) {
                    $member_characters = json_decode($member_characters_response->getBody()->getContents());
                    $member_characters = collect($member_characters);

                    $this->info('Characters: ' . collect($member_characters['Response']->characters->data)->count());

                    foreach($member_characters['Response']->characters->data as $character_id => $character) {
                        //dd($character);

                        $char_ids[] = $character_id;

                        $clan_member_character = \App\Classes\Clan_Member_Character::updateOrCreate(
                          ['id' => $character_id],
                          [
                            'user_id' => $member->destinyUserInfo->membershipId,
                            'light' => $character->light,
                            'class' => $this->class_hash[$character->classHash],
                            'date_added' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
                          ]
                        );

                        $this->info('Updated: ' . $this->class_hash[$character->classHash]);
                    }
                }
            }

            // delete character data that does not belong to members anymore
            DB::table('clan_member_characters')->whereNotIn('id', $char_ids)->delete();

            // Refresh Cache
            Cache::forget('clan_members_characters');
            Cache::forever('clan_members_characters', App\Classes\Clan_Member::with('characters')->with('platform_profile')->with('aliases')->get());

            $this->info('Completed: Characters update');
            return 1;
        }
        return 0;
    }
}
