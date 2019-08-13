<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Clan_Member_Activity_Buddy extends Model
{
  protected $table = 'clan_member_activity_buddy';
  protected $primaryKey = 'id';
  public $timestamps = false;

  protected $fillable = ['id', 'member_id', 'mode', 'buddy_id', 'activity_count', 'date_added'];

  public function member()
  {
      return $this->belongsTo('App\Classes\Clan_Member', 'member_id');
  }
}