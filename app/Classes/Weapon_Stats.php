<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Weapon_Stats extends Model
{
  protected $table = 'clan_weapon_stats';
  protected $primaryKey = 'user_id';
  protected $keyType = 'string';
  public $timestamps = false;

  public function __construct(array $attributes = array())
  {
      parent::__construct($attributes);

      foreach( $this::get_default_weapon_kills() as $key => $val ) {
        $this->$key = $val;
      }
  }

  public static function update_members($members) {
    DB::table('clan_weapon_stats')->truncate();

    foreach($members as $member) {
      $member_stat = new Weapon_Stats;

      // weapon stats
      if( isset($member->weaponKills) ) {
        foreach($member->weaponKills as $key => $val) {
          $member_stat->$key = $val;
        }
      }

      $member_stat->user_id = $member->destinyUserInfo->membershipId;
      $member_stat->last_updated = date("Y-m-d H:i:s");

      $member_stat->save();
    }
  }

  public static function get_default_weapon_kills()
  {
    return [
      'weaponKillsAutoRifle' => 0,
      'weaponKillsBeamRifle' => 0,
      'weaponKillsBow' => 0,
      'weaponKillsFusionRifle' => 0,
      'weaponKillsHandCannon' => 0,
      'weaponKillsTraceRifle' => 0,
      'weaponKillsPulseRifle' => 0,
      'weaponKillsRocketLauncher' => 0,
      'weaponKillsScoutRifle' => 0,
      'weaponKillsShotgun' => 0,
      'weaponKillsSniper' => 0,
      'weaponKillsSubmachinegun' => 0,
      'weaponKillsRelic' => 0,
      'weaponKillsSideArm' => 0,
      'weaponKillsSword' => 0,
      'weaponKillsGrenadeLauncher' => 0
    ];
  }
}