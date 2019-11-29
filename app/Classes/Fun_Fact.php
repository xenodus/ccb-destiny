<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Fun_Fact extends Model
{
  protected $table = 'fun_facts';
  protected $primaryKey = 'id';
  protected $connection = 'ccbbot';
  public $timestamps = false;
}