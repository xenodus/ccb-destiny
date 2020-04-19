<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
  protected $table = 'streams';
  protected $connection = 'discord';
  public $timestamps = false;

  public static function getMemberStreams()
  {
    $member_streams = Stream::where('server_id', env('DISCORD_SERVER_ID'))
      ->where('channel_id', env('DISCORD_SERVER_MEMBER_STREAMER_CHANNEL_ID'))
      ->get();

    return $member_streams;
  }
}