<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Cache;
use App;

class Clan_Member extends Model
{
  protected $table = 'clan_members';
  protected $primaryKey = 'id';
  public $timestamps = false;
  protected $casts = ['id' => 'string', 'bungie_id' => 'string'];
  protected $fillable = ['id', 'bungie_id', 'display_name', 'membershipType', 'last_online', 'date_added'];

  public function aliases()
  {
      return $this->hasMany('App\Classes\Clan_Member_Alias', 'user_id');
  }

  public function characters()
  {
      return $this->hasMany('App\Classes\Clan_Member_Character', 'user_id');
  }

  public function platform_profile()
  {
      return $this->hasMany('App\Classes\Clan_Member_Platform_Profile', 'id');
  }

  public function raid_activities()
  {
    return $this->hasMany('App\Classes\Clan_Member_Activity_Buddy_Instance', 'member_id')
      ->select("activity_id")
      ->where('mode', 4);
  }

  public function pvp_activities()
  {
    return $this->hasMany('App\Classes\Clan_Member_Activity_Buddy_Instance', 'member_id')
      ->select("activity_id")
      ->where('mode', 5);
  }

  public function gambit_activities()
  {
    return $this->hasMany('App\Classes\Clan_Member_Activity_Buddy_Instance', 'member_id')
      ->select("activity_id")
      ->whereIn('mode', [63, 75]);
  }

  public function get_gambit_buddies($limit=0)
  {
    $buddies = App\Classes\Clan_Member_Activity_Buddy_Instance::
      selectRaw("member_id, buddy_id, activity_id, count(buddy_id) as activity_count")
      ->where('member_id', $this->id)
      ->whereIn('mode', [63, 75])
      ->groupBy('member_id')
      ->groupBy('buddy_id')
      ->orderBy('activity_count', 'desc');

    if($limit>0) {
      $buddies = $buddies->limit($limit);
    }

    $buddies = $buddies->get();

    return $buddies;
  }

  public function get_raid_buddies($limit=0)
  {
    $buddies = App\Classes\Clan_Member_Activity_Buddy_Instance::
      selectRaw("member_id, buddy_id, activity_id, count(buddy_id) as activity_count")
      ->where('member_id', $this->id)
      ->where('mode', 4)
      ->groupBy('member_id')
      ->groupBy('buddy_id')
      ->orderBy('activity_count', 'desc');

    if($limit>0) {
      $buddies = $buddies->limit($limit);
    }

    $buddies = $buddies->get();

    return $buddies;
  }

  public function raid_buddies()
  {
    return $this->hasMany('App\Classes\Clan_Member_Activity_Buddy_Instance', 'member_id')
      ->selectRaw("member_id, buddy_id, activity_id, count(buddy_id) as activity_count")
      ->where('mode', 4)
      ->groupBy('member_id')
      ->groupBy('buddy_id')
      ->orderBy('activity_count', 'desc');
  }

  public function get_pvp_buddies($limit=0)
  {
    $buddies = App\Classes\Clan_Member_Activity_Buddy_Instance::
      selectRaw("member_id, buddy_id, activity_id, count(buddy_id) as activity_count")
      ->where('member_id', $this->id)
      ->where('mode', 5)
      ->groupBy('member_id')
      ->groupBy('buddy_id')
      ->orderBy('activity_count', 'desc');

    if($limit>0) {
      $buddies = $buddies->limit($limit);
    }

    $buddies = $buddies->get();

    return $buddies;
  }

  public function pvp_buddies()
  {
    return $this->hasMany('App\Classes\Clan_Member_Activity_Buddy_Instance', 'member_id')
      ->selectRaw("member_id, buddy_id, activity_id, count(buddy_id) as activity_count")
      ->where('mode', 5)
      ->groupBy('member_id')
      ->groupBy('buddy_id')
      ->orderBy('activity_count', 'desc');
  }

  public function pvp_stats()
  {
      return $this->hasOne('App\Classes\Pvp_Stats', 'user_id');
  }

  public static function get_members_online()
  {
    $members = self::get_members();
    $members = collect(json_decode($members));

    if( $members->count() > 0 ) {

      $online_members = $members->filter(function($member){
        return $member->isOnline != false;
      });

      $online_members = $online_members->map(function($member){
        return [
          'membershipId' => $member->destinyUserInfo->membershipId,
          'displayName' => $member->destinyUserInfo->displayName,
          'membershipType' => $member->destinyUserInfo->membershipType,
          'avatar' => isset($member->bungieNetUserInfo) ? $member->bungieNetUserInfo->iconPath : '',
          'lastOnlineStatusChange' => $member->lastOnlineStatusChange
        ];
      });

      return collect($online_members)->toJson();
    }

    return '';
  }

  public static function get_members()
  {
    $url = env('BUNGIE_API_ROOT_URL').'/GroupV2/'.env('CLAN_ID').'/Members/';

    $client = new Client(['http_errors' => false]); //GuzzleHttp\Client
    $response = $client->get($url, [
      'headers' => [
        'X-API-Key' => env('BUNGIE_API')
      ]
    ]);

    if( $response->getStatusCode() == 200 ) {
      $payload = json_decode($response->getBody()->getContents());

      // Get all current member ids and delete those are ain't in clan anymore
      $ids = collect($payload->Response->results)->filter(function($member){
        return in_array(env('BUNGIE_PC_PLATFORM_ID'), $member->destinyUserInfo->applicableMembershipTypes);
      })
      ->map(function($member){
        return $member->destinyUserInfo->membershipId;
      })->toArray();

      DB::table('clan_members')->whereNotIn('id', $ids)->delete();

      foreach($payload->Response->results as $key => $result) {
        if( in_array(env('BUNGIE_PC_PLATFORM_ID'), $result->destinyUserInfo->applicableMembershipTypes) ) {
          $last_online = \Carbon\Carbon::createFromTimestamp($result->lastOnlineStatusChange, 'UTC');
          $last_online->setTimezone('Asia/Singapore');

          if( bin2hex($result->destinyUserInfo->displayName) == 'f3a080a1f3a080a1' )
            $result->destinyUserInfo->displayName = '- empty name -';

          $clan_member = \App\Classes\Clan_Member::updateOrCreate(
            ['id' => $result->destinyUserInfo->membershipId],
            [
              'bungie_id' => isset($result->bungieNetUserInfo) ? $result->bungieNetUserInfo->membershipId : '',
              'display_name' => $result->destinyUserInfo->displayName,
              'membershipType' => $result->destinyUserInfo->membershipType,
              'last_online' => $last_online->format('Y-m-d H:i:s'),
              'date_added' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            ]
          );
        }
        else {
          unset( $payload->Response->results[$key] );
        }
      }

      // Reset array keys
      $payload->Response->results = array_values($payload->Response->results);

      // Refresh Cache
      Cache::forget('clan_members');
      Cache::forever('clan_members', App\Classes\Clan_Member::get());

      return collect($payload->Response->results)->toJson();
    }

    return '';
  }
}