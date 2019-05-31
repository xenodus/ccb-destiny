<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class News_Feed extends Model
{
  protected $connection = 'ccb_mysql';
  protected $table = 'news_feed';
  protected $primaryKey = 'id';
  public $timestamps = false;

  public function __construct(array $attributes = array())
  {
      parent::__construct($attributes);
  }
}