<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Raid_Lockouts extends Model
{
  protected $table = 'raid_lockouts';
  protected $primaryKey = 'id';
  public $timestamps = false;

  protected $casts = [ 'id' => 'string' ];

  public function member()
  {
      return $this->belongsTo('App\Classes\Clan_Member', 'id');
  }
}