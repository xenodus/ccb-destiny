<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Pvp_Stats extends Model
{
  protected $table = 'clan_pvp_stats';
  protected $primaryKey = 'user_id';
  public $timestamps = false;

  public function __construct(array $attributes = array())
  {
      parent::__construct($attributes);

      foreach( $this::get_default_pvp_stats() as $key => $val ) {
        $this->$key = $val;
      }
  }

  public static function update_members($members) {
    DB::table('clan_pvp_stats')->truncate();

    foreach($members as $member) {
      $member_stat = new Pvp_Stats;

      // pvp stats
      if( isset($member->pvpStats) ) {
        foreach($member->pvpStats as $key => $val) {
          $member_stat->$key = $val;
        }
      }

      $member_stat->user_id = $member->destinyUserInfo->membershipId;
      $member_stat->last_updated = date("Y-m-d H:i:s");

      $member_stat->save();
    }
  }

  public static function get_default_pvp_stats()
  {
    return [
      'gold_medals' => 0,
      'kd' => 0,
      'kda' => 0,
      'kad' => 0,
      'glory' => 0,
      'valor' => 0,
      'glory_step' => 0,
      'valor_step' => 0,
      'valor_resets' => 0,
      'weaponKillsSuper' => 0,
      'weaponKillsMelee' => 0,
      'weaponKillsGrenade' => 0
    ];
  }
}