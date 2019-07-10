<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class News_Feed extends Model
{
  protected $table = 'news_feed';
  protected $primaryKey = 'id';
  public $timestamps = false;
}