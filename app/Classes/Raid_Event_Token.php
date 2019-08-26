<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Raid_Event_Token extends Model
{
  protected $table = 'event_token';
  protected $primaryKey = 'id';
  protected $connection = 'discord';
  public $timestamps = false;
}