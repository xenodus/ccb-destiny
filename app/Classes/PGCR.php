<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use DB;

// Post Game Carnage Report

class PGCR extends Model
{
  protected $table = 'clan_member_pgcr';
  protected $primaryKey = 'id';
  public $timestamps = false;
  protected $keyType = 'string';
  protected $casts = ['id' => 'string'];
  protected $fillable = ['id', 'pgcr', 'date_added'];
}