<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Clan_Member extends Model
{
  protected $table = 'clan_members';
  protected $primaryKey = 'id';
  public $timestamps = false;

  protected $fillable = ['id', 'display_name', 'last_online', 'date_added'];

  public function characters()
  {
      return $this->hasMany('App\Classes\Clan_Member_Character', 'user_id');
  }

  public function pvp_stats()
  {
      return $this->hasOne('App\Classes\Pvp_Stats', 'user_id');
  }
}