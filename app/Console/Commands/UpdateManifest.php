<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;
use Storage;
use JsonMachine\JsonMachine;

class UpdateManifest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:manifest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update manifest';

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
      ini_set('memory_limit', '1024M');

      // Get manifest JSON path
      $client = new Client(); //GuzzleHttp\Client
      $manifest_response = $client->get(
        env('BUNGIE_API_ROOT_URL').'/Destiny2/Manifest/',
        ['headers' => ['X-API-Key' => env('BUNGIE_API')], 'http_errors' => false]
      );

      if( $manifest_response->getStatusCode() == 200 ) {

        $this->info('Begin: Manifest Update');

        $manifest = json_decode($manifest_response->getBody()->getContents());
        $manifest = collect($manifest);
        $manifest_endpoint = $manifest['Response']->jsonWorldContentPaths->en;

        if( isset($manifest_endpoint) ) {

          $this->info('https://www.bungie.net'.$manifest_endpoint);

          $manifest_contents = file_get_contents( 'https://www.bungie.net'.$manifest_endpoint );
          file_put_contents(storage_path('manifest/manifest.json'), $manifest_contents);

          $jsonStream = JsonMachine::fromFile(storage_path('manifest/manifest.json'));

          foreach($jsonStream as $name => $data) {
            // echo $name."\n";
            $this->info("Processing: " . $name);
            $data_json = json_encode($data);
            file_put_contents(storage_path('manifest/'.$name.'.json'), $data_json);
          }
        }
      }

      $this->info('Completed: Manifest Update');
      return 1;
    }
}
