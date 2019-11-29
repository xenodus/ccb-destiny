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

class FunFactController extends Controller
{
    public static function isTokenExpired($token)
    {
      $is_expired = true;

      $fun_facts_token = App\Classes\Fun_Facts_Token::where('token', $token)->where('status', 'active')->first();

      if( $fun_facts_token ) {
        $expiry = Carbon::parse( $fun_facts_token->expires );

        if( $expiry->greaterThan( Carbon::now() ) ) {
          $is_expired = false;
        }
      }

      return $is_expired;
    }

    public function web_admin(Request $request, $token, $type='active')
    {
      $data['hide_header'] = 1;
      $data['hide_footer'] = 1;
      $data['token'] = $token;
      $data['type'] = $type;

      $data['expired'] = FunFactController::isTokenExpired($token);

      if( $data['expired'] == false ) {

        $data['ff_token'] = App\Classes\Fun_Facts_Token::where('token', $token)->first();
        $data['active_count'] = App\Classes\Fun_Fact::where('status', 'active')->count();
        $data['deleted_count'] = App\Classes\Fun_Fact::where('status', 'disabled')->count();

        if( $type=='disabled' ) {
          $data['fun_facts'] = App\Classes\Fun_Fact::where('status', 'disabled')->orderBy('id', 'desc')->paginate(50);
        }
        else {
          $data['fun_facts'] = App\Classes\Fun_Fact::where('status', 'active')->orderBy('id', 'desc')->paginate(50);
        }
      }

      return view('funfacts.index', $data);
    }

    public function web_create_process(Request $request, $token='')
    {
      $new_fun_fact = $request->input('fun_fact');

      $status = 1;
      $statuses = [
        0 => 'Success',
        1 => 'Invalid token or session has expired. Please request a new session through CCB Bot.',
        2 => 'Failed to create new Fun Fact. At least 10 characters are required.'
      ];

      if( $token ) {
        $is_token_expired = FunFactController::isTokenExpired($token);

        if( $is_token_expired == false ) {

          if( strlen($new_fun_fact) < 10 ) {
            $status = 2;
          }
          else {
            // do create
            $fun_facts_token = App\Classes\Fun_Facts_Token::where('token', $token)->where('status', 'active')->first();
            $ff = new App\Classes\Fun_Fact();
            $ff->fact = $new_fun_fact;
            $ff->status = 'active';
            $ff->created_by_discord_id = $fun_facts_token->discord_id;
            $ff->created_by_discord_nickname = $fun_facts_token->discord_nickname;
            $ff->updated_by_discord_id = $fun_facts_token->discord_id;
            $ff->updated_by_discord_nickname = $fun_facts_token->discord_nickname;
            $ff->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
            $ff->save();

            $status = 0;
          }
        }
      }

      $results['status'] = $status;
      $results['message'] = $statuses[$status];

      return response()->json($results);
    }

    public function web_edit_process(Request $request, $token)
    {
      $edit_fun_fact = $request->input('fun_fact');
      $fun_fact_id = $request->input('ff_id');
      $fun_fact_status = $request->input('ff_status');

      $status = 1;
      $statuses = [
        0 => 'Success',
        1 => 'Invalid token or session has expired. Please request a new session through CCB Bot.',
        2 => 'Failed to edit new Fun Fact. At least 10 characters are required.',
        3 => 'Unable to fetch Fun Fact'
      ];

      if( $token ) {
        $is_token_expired = FunFactController::isTokenExpired($token);

        if( $is_token_expired == false ) {

          if( strlen($edit_fun_fact) < 10 ) {
            $status = 2;
          }
          else {
            // Do edit
            $fun_facts_token = App\Classes\Fun_Facts_Token::where('token', $token)->where('status', 'active')->first();

            $ff = App\Classes\Fun_Fact::find( $fun_fact_id );

            // Status Change
            if( $fun_fact_status == 'active' )
              $ff->status = 'active';
            else if( $fun_fact_status == 'disabled' )
              $ff->status = 'disabled';

            $ff->fact = $edit_fun_fact;
            $ff->updated_by_discord_id = $fun_facts_token->discord_id;
            $ff->updated_by_discord_nickname = $fun_facts_token->discord_nickname;
            $ff->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
            $ff->save();

            $status = 0;
          }
        }
      }

      $results['status'] = $status;
      $results['message'] = $statuses[$status];

      return response()->json($results);
    }
}