<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use Cookie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Classes\Post;

class AlfredController extends Controller
{
    public function web_create_edit(Request $request, $token)
    {
      $data['hide_header'] = 1;
      $data['hide_footer'] = 1;
      $data['token'] = $token;
      $data['expired'] = true;

      $raid_event_token = App\Classes\Raid_Event_Token::where('token', $token)->where('status', 'active')->first();

      if( $raid_event_token ) {
        $expiry = Carbon::parse( $raid_event_token->expires );

        if( $expiry->greaterThan( Carbon::now() ) ) {

          $data['expired'] = false;

          if( $raid_event_token->event_id ) {
            $raid_event = App\Classes\Raid_Event::find( $raid_event_token->event_id );

            if( $raid_event ) {
              $data['raid_event'] = $raid_event;
            }
          }
        }
      }

      return view('alfred.event', $data);
    }

    public function web_create_edit_process(Request $request, $token)
    {
      $data['hide_header'] = 1;
      $data['hide_footer'] = 1;
      $data['token'] = $token;
      $data['expired'] = true;

      $raid_event_token = App\Classes\Raid_Event_Token::where('token', $token)->where('status', 'active')->first();

      if( $raid_event_token ) {
        $expiry = Carbon::parse( $raid_event_token->expires );

        if( $expiry->greaterThan( Carbon::now() ) ) {
          $data['expired'] = false;
        }

        // Process Post Request
        Validator::make($request->all(), [
          'event_name' => 'required|max:255',
          'event_description' => 'max:1000',
          'event_datetime' => 'required|date_format:j M Y g:i A',
        ])->validate();

        // Create / Update
        $event_name = Carbon::parse( $request->input('event_datetime') )->format('j M g:iA') . " [" . $request->input('event_name') . "]";
        $event_description = $request->input('event_description') ?? '';
        $event_date = Carbon::parse( $request->input('event_datetime') )->format('Y-m-d H:i:s');

        // Invalidate Token
        $raid_event_token->status = 'disabled';
        $raid_event_token->save();

        if( $raid_event_token->event_id ) {

          $raid_event = App\Classes\Raid_Event::find( $raid_event_token->event_id );

          if( $raid_event ) {
            $raid_event->event_name = $event_name;
            $raid_event->event_description = $event_description;
            $raid_event->event_date = $event_date;
            $raid_event->status = 'deleted';
            $raid_event->save();

            $new_raid_event = new App\Classes\Raid_Event();
            $new_raid_event->server_id = $raid_event->server_id;
            $new_raid_event->channel_id = $raid_event->channel_id;
            $new_raid_event->event_name = $raid_event->event_name;
            $new_raid_event->event_description = $raid_event->event_description;
            $new_raid_event->event_date = $raid_event->event_date;
            $new_raid_event->date_added = 'active';
            $new_raid_event->created_by = $raid_event->created_by;
            $new_raid_event->created_by_username = $raid_event->created_by_username;
            $new_raid_event->date_added = $raid_event->date_added;
            $new_raid_event->save();

            App\Classes\Raid_Signup::where('event_id', $raid_event->event_id)->update(['event_id' => $new_raid_event->event_id]);

            return redirect()->route('alfred_web_create_edit', [$token])->with('status', "Raid event updated! It'll appear in your Discord channel soon.");
          }
        }
        else {
          $raid_event = new App\Classes\Raid_Event();
          $raid_event->server_id = $raid_event_token->server_id;
          $raid_event->channel_id = $raid_event_token->channel_id;
          $raid_event->event_name = $event_name;
          $raid_event->event_description = $event_description;
          $raid_event->event_date = $event_date;
          $raid_event->created_by = $raid_event_token->user_id;
          $raid_event->created_by_username = $raid_event_token->username;
          $raid_event->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
          $raid_event->save();

          $raid_signup = new App\Classes\Raid_Signup();
          $raid_signup->user_id = $raid_event_token->user_id;
          $raid_signup->username = $raid_event_token->username;
          $raid_signup->event_id = $raid_event->event_id;
          $raid_signup->type = 'confirmed';
          $raid_signup->added_by_user_id = $raid_event_token->user_id;
          $raid_signup->added_by_username = $raid_event_token->username;
          $raid_signup->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
          $raid_signup->save();

          return redirect()->route('alfred_web_create_edit', [$token])->with('status', "Raid event created! It'll appear in your Discord channel soon.");
        }
      }
      else {
        abort(404);
      }
    }
}