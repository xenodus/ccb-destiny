<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use App;

class MHW_Monster extends Model
{
  protected $table = 'mhw_monsters';
  protected $primaryKey = 'id';
  public $timestamps = false;

  public function weak_points()
  {
      return $this->hasMany('App\Classes\MHW_Monster_Weak_Point', 'monster_id');
  }
}