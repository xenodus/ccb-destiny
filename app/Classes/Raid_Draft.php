<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;

class Raid_Draft extends Model
{
  protected $table = 'raid_draft';
  protected $primaryKey = 'id';
  public $timestamps = false;

  protected $fillable = ['id', 'event_id', 'signup_id', 'team', 'is_lead', 'date_added'];
}