<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
use DB;
use Cache;

class UpdateVendors extends Command
{
    private $vendorHash = [
      /* 'Ada-1': '2917531897', */
      'Suraya Hawthorne' => '3347378076',
      'Banshee-44' => '672118013',
      'Spider' => '863940356',
      'Lord Shaxx' => '3603221665',
      'The Drifter' => '248695599',
      'Lord Saladin' => '895295461',
      'Commander Zavala' => '69482069',
      'Xur' => '2190858386',
      'Tess Everis' => '3361454721',
      'Petra Venj' => '1841717884',
      'Benedict 99-40' => '1265988377',
      'Eva Levante' => '919809084'
    ];

    // traits = stats, restore default == shader
    private $perksExcluded = ['Trait', 'Restore Defaults', 'General Armor Mod'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:vendors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get and update vendor sale items.';

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

        // 1. Get and update oauth token

        $token = App\Classes\Bungie_OAuth::orderBy('id', 'desc')->first();
        $token = json_decode($token->data);
        $refresh_token = $token->refresh_token;

        //dd($refresh_token);
        //dd('https://www.bungie.net/en/oauth/authorize?client_id='.env('BUNGIE_OAUTH_CLIENT_ID').'&response_type=code');
        //$authorization_code = 'e5fad4d63d8a8c09b7cdefccd0c37b70';

        $tokenRefreshEndpoint = 'https://www.bungie.net/platform/app/oauth/token/';

        $client = new Client(); //GuzzleHttp\Client
        $refresh_token_response = $client->post(
            $tokenRefreshEndpoint,
            [
                'headers' => [
                    'Authorization' => 'Basic '.base64_encode(env('BUNGIE_OAUTH_CLIENT_ID').':'.env('BUNGIE_OAUTH_CLIENT_SECRET'))
                ],
                'http_errors' => false,
                'form_params' => [
                    //'grant_type' => 'authorization_code',
                    //'code' => $authorization_code,
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refresh_token,
                    'client_id' => env('BUNGIE_OAUTH_CLIENT_ID'),
                    'client_secret' => env('BUNGIE_OAUTH_CLIENT_SECRET')
                ]
            ]
        );

        if( $refresh_token_response->getStatusCode() == 200 ) {

            $this->info('Begin: Token Update');

            $new_token_data = (string) $refresh_token_response->getBody(); // json string
            $new_token = new App\Classes\Bungie_OAuth();
            $new_token->data = $new_token_data;
            $new_token->date_added = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
            $new_token->save();

            $this->info('New Token: ' . $new_token_data);
            $this->info('Completed: Token Update');

            $access_token = json_decode($new_token_data)->access_token;

            // 2. Get Item Definitions
            $item_definitions = collect(json_decode(file_get_contents(storage_path('manifest/DestinyInventoryItemDefinition.json'))));

            $this->info('Item Definition Count: ' . $item_definitions->count());

            // 3. Get Vendor Data
            $vendor_response = $client->get(
                env('BUNGIE_API_ROOT_URL').'/Destiny2/'.env('BUNGIE_PC_PLATFORM_ID').'/Profile/'.env('DESTINY_ID').'/Character/'.env('DESTINY_CHAR_ID').'/Vendors?components=402,400,302,305',
                [
                    'headers' => [
                        'X-API-Key' => env('BUNGIE_API'),
                        'Authorization' => 'Bearer '.$access_token,
                    ],
                    'http_errors' => false
                ]
            );

            if( $vendor_response->getStatusCode() == 200 ) {

                $this->info('Begin: Vendor Updates');

                DB::table('vendor_sales')->delete(); // cleanup db
                DB::table('vendor_sales_item_cost')->delete(); // cleanup db
                DB::table('vendor_sales_item_perks')->delete(); // cleanup db

                $vendor_data = json_decode($vendor_response->getBody()->getContents());
                $vendor_data = collect($vendor_data);

                $saleItems = [];

                $this->info('Begin: Vendor Updates');

                foreach($this->vendorHash as $vendor_name => $vendor_hash) {

                    $saleItems[$vendor_name] = [];

                    if( isset( $vendor_data['Response']->sales->data->$vendor_hash ) ) {

                        foreach($vendor_data['Response']->sales->data->$vendor_hash->saleItems as $k => $v) {

                            // 4. Get Sale Item Perks' Hash
                            $perks = [];

                            if( isset( $vendor_data['Response']->itemComponents->$vendor_hash->sockets->data->$k ) ) {
                                if(count($vendor_data['Response']->itemComponents->$vendor_hash->sockets->data->$k->sockets) > 0){

                                    $sockets = $vendor_data['Response']->itemComponents->$vendor_hash->sockets->data->$k->sockets;

                                    foreach($sockets as $socket) {
                                        if(
                                            isset($socket->plugHash) &&
                                            isset($item_definitions[$socket->plugHash]) &&
                                            isset($item_definitions[$socket->plugHash]->itemTypeDisplayName) &&
                                            isset($socket->isVisible) && $socket->isVisible == true &&
                                            $item_definitions[$socket->plugHash]->itemTypeDisplayName != '' &&
                                            $item_definitions[$socket->plugHash]->displayProperties->name != 'Empty Mod Socket' &&
                                            in_array($item_definitions[$socket->plugHash]->itemTypeDisplayName, $this->perksExcluded) == false
                                        ) {
                                            /*
                                            $perkGroup = [];

                                            if( $v->itemHash == 1508896098 ) {
                                                if( !in_array($item_definitions[$socket->plugHash]->hash, [2473404935, 2420895100, 3465198467]) )
                                                dd( $item_definitions[$socket->plugHash] );
                                            }

                                            $perkGroup[] = $item_definitions[ $socket->plugHash ];
                                            $perks[] = $perkGroup;
                                            */

                                            $perks[] = $item_definitions[ $socket->plugHash ];
                                        }
                                    }
                                }
                                //dd( $vendor_data['Response']->itemComponents->$vendor_hash->sockets->data->$k->sockets );
                            }

                            // 5. Prepare Data
                            $saleItems[$vendor_name][] = [
                                'hash' => $v->itemHash,
                                'cost' => $v->costs,
                                'perks' => $perks
                            ];
                        }
                    }
                }

                //dd();
                //dd( $saleItems['Xur'] );

                // 6. Process Data
                $n = 1;
                $date_added = \Carbon\Carbon::now()->format('Y-m-d H:00:00');

                foreach($saleItems as $vendor_name => $items) {

                    $this->info('Processing '.$n.' of '.count($saleItems).': ' . $vendor_name);
                    $n++;

                    foreach($items as $sale_item) {

                        // 7. Create Sale Item
                        $itemHash = $sale_item['hash'];

                        if( $item_definitions[$itemHash]->displayProperties->name ) {

                            $costHash = $sale_item['cost'][0]->itemHash ?? null;
                            $costAmount = $sale_item['cost'][0]->quantity ?? null;
                            $costName = $item_definitions[$costHash]->displayProperties->name ?? null;

                            $vendor_sales = new App\Classes\Vendor_Sales();
                            $vendor_sales->itemTypeDisplayName = $item_definitions[$itemHash]->itemTypeDisplayName ?? '';
                            $vendor_sales->itemTypeAndTierDisplayName = $item_definitions[$itemHash]->itemTypeAndTierDisplayName ?? '';
                            $vendor_sales->hash = $item_definitions[$itemHash]->hash;
                            $vendor_sales->vendor_hash = $this->vendorHash[$vendor_name];
                            $vendor_sales->description = $item_definitions[$itemHash]->displayProperties->description;
                            $vendor_sales->name = str_replace('Purchase ', '', $item_definitions[$itemHash]->displayProperties->name);
                            $vendor_sales->icon = $item_definitions[$itemHash]->displayProperties->hasIcon == true ? $item_definitions[$itemHash]->displayProperties->icon : '';
                            $vendor_sales->cost = $costAmount;
                            $vendor_sales->cost_hash = $costHash;
                            $vendor_sales->cost_name = $costName;
                            $vendor_sales->date_added = $date_added;

                            try {
                                $vendor_sales->save();
                                $this->info("Created: " . $vendor_sales->name);
                            }
                            catch(\PDOException $e) {
                                //$this->info($e->getMessage());
                                $this->info("Error: " . $vendor_sales->name);
                            }

                            // Detailed Sale Item Cost
                            if( $vendor_sales->id && isset($sale_item['cost']) && count( $sale_item['cost'] ) ) {
                                foreach( $sale_item['cost'] as $cost ) {
                                    $vendor_sales_item_cost = new App\Classes\Vendor_Sales_Item_Cost();
                                    $vendor_sales_item_cost->vendor_sales_id = $vendor_sales->id;
                                    $vendor_sales_item_cost->cost_hash = $cost->itemHash ?? null;
                                    $vendor_sales_item_cost->cost_quantity = $cost->quantity ?? null;
                                    $vendor_sales_item_cost->cost_name = $item_definitions[ $cost->itemHash ]->displayProperties->name ?? null;
                                    $vendor_sales_item_cost->cost_icon = $item_definitions[ $cost->itemHash ]->displayProperties->hasIcon == true ? $item_definitions[ $cost->itemHash ]->displayProperties->icon : '';
                                    $vendor_sales_item_cost->date_added = $date_added;

                                    $vendor_sales_item_cost->save();
                                }
                            }

                            // 8. Create Perks for Sale Item
                            if( count($sale_item['perks']) > 0 && $vendor_sales->id ) {

                                foreach($sale_item['perks'] as $perk) {
                                    $vendor_sales_item_perk = new App\Classes\Vendor_Sales_Item_Perks();
                                    $vendor_sales_item_perk->vendor_sales_id = $vendor_sales->id;
                                    $vendor_sales_item_perk->perk_group = 0;
                                    $vendor_sales_item_perk->date_added = $date_added;
                                    $vendor_sales_item_perk->description = $perk->displayProperties->description;
                                    $vendor_sales_item_perk->name = $perk->displayProperties->name;
                                    $vendor_sales_item_perk->icon = $perk->displayProperties->hasIcon == true ? $perk->displayProperties->icon : '';
                                    $vendor_sales_item_perk->itemTypeDisplayName = $perk->itemTypeDisplayName;
                                    $vendor_sales_item_perk->itemTypeAndTierDisplayName = $perk->itemTypeAndTierDisplayName;
                                    $vendor_sales_item_perk->hash = $perk->hash;
                                    $vendor_sales_item_perk->save();
                                }

                                /*
                                foreach($sale_item['perks'] as $perk_group_index => $perk_group) { // perk group
                                    foreach($perk_group as $perk) { // individual perk hash
                                        $vendor_sales_item_perk = new App\Classes\Vendor_Sales_Item_Perks();
                                        $vendor_sales_item_perk->vendor_sales_id = $vendor_sales->id;
                                        $vendor_sales_item_perk->perk_group = $perk_group_index;
                                        $vendor_sales_item_perk->date_added = $date_added;
                                        $vendor_sales_item_perk->description = $perk->displayProperties->description;
                                        $vendor_sales_item_perk->name = $perk->displayProperties->name;
                                        $vendor_sales_item_perk->icon = $perk->displayProperties->hasIcon == true ? $perk->displayProperties->icon : '';
                                        $vendor_sales_item_perk->itemTypeDisplayName = $perk->itemTypeDisplayName;
                                        $vendor_sales_item_perk->itemTypeAndTierDisplayName = $perk->itemTypeAndTierDisplayName;
                                        $vendor_sales_item_perk->hash = $perk->hash;
                                        $vendor_sales_item_perk->save();
                                    }
                                }
                                */
                            }
                        }
                    }
                }

                /*
                $deletedRows1 = App\Classes\Vendor_Sales::where('date_added', '!=', $date_added)->delete();
                $deletedRows2 = App\Classes\Vendor_Sales_Item_Perks::where('date_added', '!=', $date_added)->delete();
                $deletedRows3 = App\Classes\Vendor_Sales_Item_Cost::where('date_added', '!=', $date_added)->delete();

                $this->info('Cleanup: '.$deletedRows1.' Vendor Sale Records Deleted');
                $this->info('Cleanup: '.$deletedRows2.' Vendor Sale Item Perks Records Deleted');
                $this->info('Cleanup: '.$deletedRows3.' Vendor Sale Item Cost Records Deleted');
                */

                $this->info('Completed: Vendor Updates');

                // Refresh Cache
                Cache::forget('vendor_sales');
                Cache::forever('vendor_sales', App\Classes\Vendor_Sales::with('costs')->orderBy('vendor_hash')->get());

                // Refresh Cache
                Cache::forget('vendor_sales_item_perks');
                Cache::forever('vendor_sales_item_perks', App\Classes\Vendor_Sales_Item_Perks::get());

                // Refresh Cache
                $vendor_sales_item_perks_xur = App\Classes\Vendor_Sales_Item_Perks::whereHas('vendor_sales', function($q) {
                    $q->where('vendor_hash', '2190858386');
                })->get();

                Cache::forget('vendor_sales_item_perks_xur');
                Cache::forever('vendor_sales_item_perks_xur', $vendor_sales_item_perks_xur);

                return 1;
            }
            else {
                $this->info('Unable To Get Vendor Data');
            }
        }
        else {
          $this->info('Unable To Authenticate with Refresh Token');
        }
    }
}
