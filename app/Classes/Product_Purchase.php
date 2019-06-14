<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Product_Purchase extends Model
{
  protected $connection = 'ccb_mysql';
  protected $table = 'product_purchase';
  protected $primaryKey = 'id';
  public $timestamps = false;
}