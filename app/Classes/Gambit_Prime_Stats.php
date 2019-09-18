<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Gambit_Prime_Stats extends Model
{
  protected $table = 'clan_gambit_prime_stats';
  protected $primaryKey = 'user_id';
  public $timestamps = false;

  public function __construct(array $attributes = array())
  {
      parent::__construct($attributes);

      foreach( $this::get_default_gambit_stats() as $key => $val ) {
        $this->$key = $val;
      }
  }

  public static function update_members($members) {
    DB::table('clan_gambit_prime_stats')->truncate();

    foreach($members as $member) {
      $member_stat = new Gambit_Prime_Stats;

      // pvp stats
      if( isset($member->gambitStats) ) {
        foreach($member->gambitStats as $key => $val) {
          $member_stat->$key = $val;
        }
      }

      $member_stat->user_id = $member->destinyUserInfo->membershipId;
      $member_stat->last_updated = date("Y-m-d H:i:s");

      $member_stat->save();
    }
  }

  public static function get_default_gambit_stats()
  {
    return [
      'infamy' => 0,
      'infamy_step' => 0,
      'infamy_resets' => 0,
      'activitiesEntered' => 0,
      'activitiesWon' => 0,
      'kills' => 0,
      'deaths' => 0,
      'killsDeathsRatio' => 0,
      'suicides' => 0,
      'efficiency' => 0,
      'invasionKills' => 0,
      'invaderKills' => 0,
      'invaderDeaths' => 0,
      'primevalDamage' => 0,
      'primevalHealing' => 0,
      'motesDeposited' => 0,
      'motesDenied' => 0,
      'motesLost' => 0,
      'smallBlockersSent' => 0,
      'mediumBlockersSent' => 0,
      'largeBlockersSent' => 0
    ];
  }
}