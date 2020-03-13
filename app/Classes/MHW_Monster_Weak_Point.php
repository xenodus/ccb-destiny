<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use App;

class MHW_Monster_Weak_Point extends Model
{
  protected $table = 'mhw_monster_weak_points';
  protected $primaryKey = 'id';
  public $timestamps = false;

  public function monster()
  {
      return $this->belongsTo('App\Classes\MHW_Monster', 'monster_id');
  }
}