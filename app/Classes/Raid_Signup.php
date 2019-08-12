<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;

class Raid_Signup extends Model
{
  protected $table = 'event_signup';
  protected $primaryKey = 'event_signup_id';
  protected $connection = 'discord';
  public $timestamps = false;
}