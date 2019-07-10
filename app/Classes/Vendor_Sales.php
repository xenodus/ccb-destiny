<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Vendor_Sales extends Model
{
  protected $table = 'vendor_sales';
  protected $primaryKey = 'id';
  public $timestamps = false;

  public function perks()
  {
      return $this->hasMany('App\Classes\Vendor_Sales_Item_Perks', 'vendor_sales_id', 'id');
  }
}