<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Clan_Member_Alias extends Model
{
  protected $table = 'clan_member_aliases';
  protected $primaryKey = 'id';
  public $timestamps = false;
  protected $casts = ['user_id' => 'string'];
  protected $fillable = ['id', 'user_id', 'name', 'date_added'];

  public function member()
  {
      return $this->belongsTo('App\Classes\Clan_Member', 'user_id');
  }
}