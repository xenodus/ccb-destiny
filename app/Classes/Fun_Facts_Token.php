<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Fun_Facts_Token extends Model
{
  protected $table = 'fun_facts_token';
  protected $primaryKey = 'id';
  protected $connection = 'ccbbot';
  public $timestamps = false;
}