<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Trials_Stats extends Model
{
  protected $table = 'clan_trials_stats';
  protected $primaryKey = 'user_id';
  protected $keyType = 'string';
  public $timestamps = false;

  public function __construct(array $attributes = array())
  {
      parent::__construct($attributes);

      foreach( $this::get_default_trials_stats() as $key => $val ) {
        $this->$key = $val;
      }
  }

  public static function update_members($members) {
    DB::table('clan_trials_stats')->truncate();

    foreach($members as $member) {
      $member_stat = new Trials_Stats;

      // pvp stats
      if( isset($member->trialsStats) ) {
        foreach($member->trialsStats as $key => $val) {
          $member_stat->$key = $val;
        }
      }

      $member_stat->user_id = $member->destinyUserInfo->membershipId;
      $member_stat->last_updated = date("Y-m-d H:i:s");

      $member_stat->save();
    }
  }

  public static function get_default_trials_stats()
  {
    return [
      'kd' => 0,
      'kda' => 0,
      'kad' => 0,
      'activitiesEntered' => 0,
      'activitiesWon' => 0,
      'weaponKillsSuper' => 0,
      'weaponKillsMelee' => 0,
      'weaponKillsGrenade' => 0,
      'flawless' => 0,
      'weaponBestType' => '',
      'combatRating' => 0
    ];
  }
}