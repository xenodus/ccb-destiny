<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

class Application extends Model
{
  protected $table = 'applications';
  protected $primaryKey = 'id';
  public $timestamps = false;

  protected $fillable = ['id', 'username', 'country', 'age', 'expansion', 'activity', 'experience', 'date_added'];
}