<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;

class Raid_Event extends Model
{
  protected $table = 'event';
  protected $primaryKey = 'event_id';
  protected $connection = 'discord';
  public $timestamps = false;

  public function signups()
  {
      return $this->hasMany('App\Classes\Raid_Signup', 'event_id');
  }

  public function getEventName()
  {
    preg_match('/\[(.*)\]/', $this->event_name, $matches);

    if( count($matches) )
      return $matches[1];
    else
      return $this->event_name;
  }

  public function getConfirmed()
  {
    $confirmed = $this->signups()->where('type', 'confirmed')->get();

    return $confirmed->pluck('username')->all();
  }

  public function getReserve()
  {
    $confirmed = $this->signups()->where('type', 'reserve')->get();

    return $confirmed->pluck('username')->all();
  }
}