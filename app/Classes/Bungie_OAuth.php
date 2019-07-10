<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Bungie_OAuth extends Model
{
  protected $table = 'oauth_token';
  protected $primaryKey = 'id';
  public $timestamps = false;
}