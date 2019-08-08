<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Activity_Modifier extends Model
{
  protected $table = 'activity_modifiers';
  protected $primaryKey = 'id';
  public $timestamps = false;

  protected $fillable = ['id', 'type', 'hash', 'description', 'name', 'icon', 'date_added'];
}