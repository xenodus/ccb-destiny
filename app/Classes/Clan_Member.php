<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Clan_Member extends Model
{
  protected $connection = 'ccb_mysql';
  protected $table = 'clan_members';
  protected $primaryKey = 'id';
  public $timestamps = false;

  protected $fillable = ['id', 'display_name', 'last_online', 'date_added'];

  public function characters()
  {
      return $this->hasMany('App\Classes\Clan_Member_Character', 'user_id');
  }
}