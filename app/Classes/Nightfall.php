<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;

class Nightfall extends Model
{
  protected $connection = 'ccb_mysql';
  protected $table = 'nightfall';
  protected $primaryKey = 'id';
  public $timestamps = false;
}