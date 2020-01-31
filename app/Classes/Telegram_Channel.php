<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;

class Telegram_Channel extends Model
{
  protected $table = 'channels';
  protected $primaryKey = 'id';
  protected $connection = 'telegrambot';
  public $timestamps = false;
}