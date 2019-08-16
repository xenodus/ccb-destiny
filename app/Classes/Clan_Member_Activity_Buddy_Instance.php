<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Clan_Member_Activity_Buddy_Instance extends Model
{
  protected $table = 'clan_member_activity_buddy_instance';
  protected $primaryKey = 'id';
  public $timestamps = false;
  protected $casts = ['buddy_id' => 'string', 'member_id' => 'string', 'activity_id' => 'string'];
  protected $fillable = ['id', 'member_id', 'mode', 'buddy_id', 'activity_id', 'pgcr', 'date_added'];

  public function member()
  {
      return $this->belongsTo('App\Classes\Clan_Member', 'member_id');
  }

  public function pgcr()
  {
      return $this->hasOne('App\Classes\PGCR', 'id', 'activity_id');
  }
}