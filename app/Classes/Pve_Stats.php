<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Pve_Stats extends Model
{
  protected $table = 'clan_pve_stats';
  protected $primaryKey = 'user_id';
  public $timestamps = false;

  public function __construct(array $attributes = array())
  {
      parent::__construct($attributes);

      foreach( $this::get_default_pve_stats() as $key => $val ) {
        $this->$key = $val;
      }
  }

  public static function get_total_kills() {
    $pk = self::selectRaw('SUM(kills) as total')->first();

    if( $pk )
      return $pk->total;
    else
      return 0;
  }

  public static function update_members($members) {
    DB::table('clan_pve_stats')->truncate();

    foreach($members as $member) {
      $member_stat = new Pve_Stats;

      // pve stats
      if( isset($member->pveStats) ) {
        foreach($member->pveStats as $key => $val) {
          $member_stat->$key = $val;
        }
      }

      $member_stat->characters_deleted = $member->charactersDeleted ?? 0;
      $member_stat->user_id = $member->destinyUserInfo->membershipId;
      $member_stat->last_updated = date("Y-m-d H:i:s");

      $member_stat->save();
    }
  }

  public static function get_default_pve_stats()
  {
    return [
      'kills' => 0,
      'deaths' => 0,
      'suicides' => 0,
      'killsDeathsRatio' => 0,
      'activitiesCleared' => 0,
      'weaponKillsSuper' => 0,
      'weaponKillsMelee' => 0,
      'weaponKillsGrenade' => 0,
      'publicEventsCompleted' => 0,
      'heroicPublicEventsCompleted' => 0
    ];
  }
}