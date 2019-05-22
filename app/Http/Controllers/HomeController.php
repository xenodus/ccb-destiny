<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function test()
    {
        $data['site_title'] = env('SITE_NAME');
        $data['active_page'] = 'home';

        return view('test', $data);
    }

    public function home()
    {
        $data['site_title'] = env('SITE_NAME');
        $data['active_page'] = 'home';

        return view('home', $data);
    }

    public function activity()
    {
        $data = [];

        $client = new Client(); //GuzzleHttp\Client
        $res = $client->get( 'https://destiny.plumbing/en/raw/DestinyActivityDefinition.json' );

        if( $res->getStatusCode() == 200 ) {
            $activity_definitions = collect(json_decode($res->getBody()->getContents()));
            //dd( $activity_definitions->where('hash', '10898844') );
        }

        // 1. Get members online
        $res = $client->get( route('get_members_online') );

        if( $res->getStatusCode() == 200 ) {
            $members_online = collect(json_decode($res->getBody()->getContents()))->toArray();

            if( count($members_online) > 0 ) {
                // 2. Get members'
                foreach($members_online as $member) {

                    $membership_id = $member->membershipId;

                    $res = $client->get( 'https://www.bungie.net/Platform/Destiny2/4/Profile/'.$membership_id.'?components=100,204', ['headers' => ['X-API-Key' => env('BUNGIE_API')]] );

                    if( $res->getStatusCode() == 200 ) {
                        $member_profile = collect(json_decode($res->getBody()->getContents()));

                        $member_activity = new App\Classes\Member_Activity();
                        $member_activity->displayName = $member_profile['Response']->profile->data->userInfo->displayName;
                        $member_activity->lastSeen = Carbon::parse($member_profile['Response']->profile->data->dateLastPlayed)->timezone('Asia/Singapore')->format('h:i A');

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

                        $member_activity->latestActivityTime = Carbon::parse($latest_activity->dateActivityStarted)->timezone('Asia/Singapore')->format('h:i A');

                        $member_activity->latestActivity = $activity_definitions->where('hash', $latest_activity->currentActivityHash)->first();

                        // Orbit
                        if( $member_activity->latestActivity->placeHash == '2961497387' ) {
                            $member_activity->latestActivity->originalDisplayProperties->name = "Orbit";
                            $member_activity->latestActivity->displayProperties->name = "Orbit";
                        }


                        $data[] = $member_activity;

                        unset( $latest_activity );
                    }
                }
            }
        }

        return response()->json($data);
    }

    public function outbreak()
    {
        $data['site_title'] = env('SITE_NAME');
        $data['active_page'] = 'outbreak';

        return view('outbreak', $data);
    }
}