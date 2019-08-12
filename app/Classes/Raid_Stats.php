<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Raid_Stats extends Model
{
  protected $table = 'clan_raid_stats';
  protected $primaryKey = 'user_id';
  public $timestamps = false;

  protected $casts = array(
    "user_id" => "string"
  );

  public function __construct(array $attributes = array())
  {
      parent::__construct($attributes);

      foreach( $this::get_default_raid_clears() as $key => $val ) {
        $this->$key = $val;
      }
  }

  public static function get_total_raids_completed() {
    $rc = self::selectRaw('SUM((levi + levip + eow + eowp + sos + sosp + lw + sotp + cos)) as total')->first();

    if( $rc )
      return $rc->total;
    else
      return 0;
  }

  public static function update_members($members) {
    DB::table('clan_raid_stats')->truncate();

    foreach($members as $member) {
      $member_stat = new Raid_Stats;

      // raid clears
      if( isset($member->raidClears) ) {
        foreach($member->raidClears as $key => $val) {
          $member_stat->$key = $val;
        }
      }

      $member_stat->user_id = $member->destinyUserInfo->membershipId;
      $member_stat->last_updated = date("Y-m-d H:i:s");

      $member_stat->save();
    }
  }

  public static function get_default_raid_clears()
  {
    return [
      'levi'  => 0,
      'levip' => 0,
      'eow'   => 0,
      'eowp'  => 0,
      'sos'   => 0,
      'sosp'  => 0,
      'lw'    => 0,
      'sotp'  => 0,
      'petra' => 0,
      'diamond' => 0
    ];
  }
}