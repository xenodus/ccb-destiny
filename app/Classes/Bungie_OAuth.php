<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Bungie_OAuth extends Model
{
  protected $connection = 'ccb_mysql';
  protected $table = 'oauth_token';
  protected $primaryKey = 'id';
  public $timestamps = false;
}