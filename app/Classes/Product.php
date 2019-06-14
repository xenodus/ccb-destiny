<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Product extends Model
{
  protected $connection = 'ccb_mysql';
  protected $table = 'products';
  protected $primaryKey = 'id';
  public $timestamps = false;
}