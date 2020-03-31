<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;

class SendTelegramRaidNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:TelegramRaidNotifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Telegram notifications for active events';

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
        $events = App\Classes\Raid_Event::where('server_id', env('DISCORD_SERVER_ID'))
        ->where('notified', 0)
        ->where('status', 'active')
        ->where('channel_id', '!=', env('DISCORD_FOUNDERS_LFG_CHANNEL_ID')) // ignore founders channel
        ->get();

        $baseUrl = 'https://api.telegram.org/bot'.env('TELEGRAM_TOKEN').'/sendMessage?parse_mode=html&disable_web_page_preview=true';

        if( count($events) && count($channels) ) {
            foreach($channels as $channel) {

                $html = '<b><u>New CCB LFG Event'.( count($events) > 1 ? 's' : '' ).' Notification</u></b>\n';

                for($i=0; $i<count($events); $i++) {
                    $no = $i + 1;
                    $event_date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $events[$i]->event_date)->format('j M g:i A, l');
                    $event_description = preg_replace( '/<@!.*>/i', '*Discord User*', $events[$i]->event_description );
                    $event_description = preg_replace( '/<.*>/i', '', $events[$i]->event_description );

                    $html .= '\n<b>'.$no.'. '.urlencode($events[$i]->getEventName()).' @ '.$event_date.'</b>';

                    if( urlencode(trim($event_description)) ) {
                        $html .= '\n\n'.urlencode(trim($event_description));
                    }

                    $html .= '\n\n<i>By: '.urlencode(trim($events[$i]->created_by_username)).' • <a href="https://discordapp.com/channels/'.$events[$i]->server_id.'/'.$events[$i]->channel_id.'/'.$events[$i]->message_id.'">Weblink</a></i>';
                    $html .= '\n';
                }

                $html = str_replace('\n', '%0A', $html);
                $html = str_replace("\n", '%0A', $html);

                $urlParam = '&chat_id='.$channel->chat_id;
                $urlParam .= '&text='.$html;

                // Post
                $url = $baseUrl . $urlParam;

                $client = new Client(['http_errors' => false]); //GuzzleHttp\Client
                $response = $client->post($url);
            }

            // Lazy update
            foreach($events as $event) {
                $event->notified = 1;
                $event->save();
            }

            $this->info('Completed: Finished sending Telegram raid notifications');
        }
        else {
            $this->info('Completed: No Telegram raid notifications to send');
        }

        return 1;
    }
}
