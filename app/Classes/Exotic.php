<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Exotic extends Model
{
  protected $table = 'exotics';
  protected $primaryKey = 'id';
  public $timestamps = false;

  protected $fillable = ['id', 'name', 'description', 'icon', 'guide', 'date_added'];
}