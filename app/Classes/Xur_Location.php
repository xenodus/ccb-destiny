<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Xur_Location extends Model
{
  protected $table = 'xur_location';
  protected $primaryKey = 'id';
  public $timestamps = false;
}