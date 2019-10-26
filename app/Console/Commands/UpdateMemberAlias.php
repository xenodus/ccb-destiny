<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;
use Cache;

class UpdateMemberAlias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:memberAlias';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update members\' display names';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client(['http_errors' => false, 'verify' => false]); //GuzzleHttp\Client

        $char_ids = [];

        $members = App\Classes\Clan_Member::get();

        if( $members->count() > 0 ) {

            foreach($members as $member) {

              $clan_member_alias = \App\Classes\Clan_Member_Alias::firstOrCreate(
                ['user_id' => $member->id, 'name' => $member->display_name],
                ['user_id' => $member->id, 'name' => $member->display_name, 'date_added' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]
              );

            }

            return 1;
        }
        return 0;
    }
}
