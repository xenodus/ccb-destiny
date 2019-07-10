<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Vendor_Sales_Item_Perks extends Model
{
  protected $connection = 'ccb_mysql';
  protected $table = 'vendor_sales_item_perks';
  protected $primaryKey = 'id';
  public $timestamps = false;

  public function vendor_sales()
  {
      return $this->belongsTo('App\Classes\Vendor_Sales', 'vendor_sales_id', 'id');
  }
}