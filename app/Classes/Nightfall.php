<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;

class Nightfall extends Model
{
  protected $table = 'nightfall';
  protected $primaryKey = 'id';
  public $timestamps = false;
}