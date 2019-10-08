<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Seal_Completions extends Model
{
  protected $table = 'seal_completions';
  protected $primaryKey = 'id';
  protected $keyType = 'string';
  public $timestamps = false;

  protected $casts = [ 'id' => 'string' ];

  public function member()
  {
      return $this->belongsTo('App\Classes\Clan_Member', 'id');
  }
}