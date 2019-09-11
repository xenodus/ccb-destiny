<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;

class UpdateMemberPlatformProfile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:memberPlatformProfile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update members\' detailed platform (bnet) profile';

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
        $members = App\Classes\Clan_Member::get();

        $client = new Client(); //GuzzleHttp\Client

        $member_ids = [];
        $n = 1;

        $this->info('Begin: Character Platform Profile Update');

        foreach($members as $member) {

            $this->info('Processing '.$n.' of '.count($members).': ' . $member->display_name);
            $n++;

            $member_profile_response = $client->get(
                env('BUNGIE_API_ROOT_URL') . '/User/GetMembershipsById/' .$member->id. '/'.$member->membershipType.'/',
                ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
            );

            $this->info( env('BUNGIE_API_ROOT_URL') . '/User/GetMembershipsById/' .$member->id. '/'.$member->membershipType.'/' );

            if( $member_profile_response->getStatusCode() == 200 ) {
                $member_profile = json_decode($member_profile_response->getBody()->getContents());
                $member_profile = collect($member_profile);

                $member_ids[] = $member->id;

                // Create / Update
                $clan_member_pp = App\Classes\Clan_Member_Platform_Profile::updateOrCreate(
                  ['id' => $member->id],
                  [
                    'profilePicturePath' => $member_profile['Response']->bungieNetUser->profilePicturePath ?? '',
                    'blizzardDisplayName' => $member_profile['Response']->bungieNetUser->blizzardDisplayName ?? '',
                    'steamDisplayName' => $member_profile['Response']->bungieNetUser->steamDisplayName ?? '',
                    'date_added' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
                  ]
                );

                $this->info('Updated: ' . $member->display_name);
            }
        }

        // Update BNet ID
        $discord_members = DB::connection('ccbbot')->table('member_roles')->where('role', 'Member')->get();

        foreach($discord_members as $discord_member) {
            $response = $client->get(
                env('BUNGIE_API_ROOT_URL').'/Destiny2/SearchDestinyPlayer/'.env('BUNGIE_PC_PLATFORM_ID').'/'.urlencode($discord_member->nickname).'/',
                [
                    'headers' => ['X-API-Key' => env('BUNGIE_API')],
                    'http_errors' => false
                ]
            );

            if( $response->getStatusCode() == 200 ) {

                $member = json_decode($response->getBody()->getContents());
                $member = collect($member);

                if( count($member['Response']) > 0 ) {
                    $clan_member_platform_profile = App\Classes\Clan_Member_Platform_Profile::where('id', $member['Response'][0]->membershipId)->first();

                    if( $clan_member_platform_profile ) {
                        $clan_member_platform_profile->blizzardID = $member['Response'][0]->displayName;
                        $clan_member_platform_profile->save();
                    }
                }
            }
        }

        // delete member profile data that does not belong to members anymore
        DB::table('clan_member_platform_profile')->whereNotIn('id', $member_ids)->delete();

        $this->info('Completed: Character Platform Profile Update');
        return 1;
    }
}
