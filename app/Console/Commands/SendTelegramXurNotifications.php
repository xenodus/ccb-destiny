<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Goutte;
use App;

class SendTelegramXurNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:TelegramXurNotifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Telegram notifications for Xur';

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
        $channels = App\Classes\Telegram_Channel::where('notify', 1)->get();

        $baseUrl = 'https://api.telegram.org/bot'.env('TELEGRAM_TOKEN').'/sendMessage?parse_mode=html&disable_web_page_preview=true';

        // Is Xur Up? Check via vendor sales
        $xur_items = App\Classes\Vendor_Sales::where('vendor_hash', '2190858386')
            ->whereNotIn('itemTypeDisplayName', ['Challenge Card', 'Invitation of the Nine'])
            ->get();

        // Xur is around!
        if( $xur_items->count() ) {
            // Location
            $location = '';
            $client = new Goutte\Client();
            $crawler = $client->request('GET', 'https://wherethefuckisxur.com/');

            if( $crawler->filter('div.xur-location > h1')->count() ) {
                $location = $crawler->filter('div.xur-location > h1')->text();
            }

            if( $location == "Xur's fucked off" ) {
                $location = '';
            }

            if( $location ) {
                $msg = 'Xûr is up at <b>'.$location.'</b>';
            }
            else {
                $msg = 'Xûr is up but I have no idea where he is :(';
            }

            $msg .= '\nExotics on sale: <b>' . implode(', ', $xur_items->pluck('name')->toArray()) . '</b>';

            $msg = str_replace('\n', '%0A', $msg);
            $msg = str_replace("\n", '%0A', $msg);

            foreach($channels as $channel) {
                $urlParam = '&chat_id='.$channel->chat_id;
                $urlParam .= '&text='.$msg;

                // Post
                $url = $baseUrl . $urlParam;
                $client = new Client(['http_errors' => false]); //GuzzleHttp\Client
                $response = $client->post($url);
            }
        }
        else {
            $this->info('Failed: Xur sales not found');
        }

        return 1;
    }
}
