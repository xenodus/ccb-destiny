<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Clan_Member_Platform_Profile extends Model
{
  protected $table = 'clan_member_platform_profile';
  protected $primaryKey = 'id';
  public $timestamps = false;

  protected $fillable = ['id', 'profilePicturePath', 'blizzardDisplayName', 'date_added'];

  public function member()
  {
    return $this->belongsTo('App\Classes\Clan_Member', 'id');
  }
}