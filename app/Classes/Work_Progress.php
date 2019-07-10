<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Work_Progress extends Model
{
  protected $table = 'work_progress';
  protected $primaryKey = 'id';
  public $timestamps = false;
}