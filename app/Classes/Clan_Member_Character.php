<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Clan_Member_Character extends Model
{
  protected $table = 'clan_member_characters';
  protected $primaryKey = 'id';
  public $timestamps = false;
  protected $casts = ['id' => 'string', 'user_id' => 'string'];
  protected $fillable = ['id', 'user_id', 'light', 'class', 'date_added'];

  public function member()
  {
      return $this->belongsTo('App\Classes\Clan_Member', 'user_id');
  }
}