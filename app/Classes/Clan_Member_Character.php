<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Clan_Member_Character extends Model
{
  protected $connection = 'ccb_mysql';
  protected $table = 'clan_member_characters';
  protected $primaryKey = 'id';
  public $timestamps = false;

  protected $fillable = ['id', 'user_id', 'light', 'class', 'date_added'];

  public function member()
  {
      return $this->belongsTo('App\Classes\Clan_Member', 'user_id');
  }
}