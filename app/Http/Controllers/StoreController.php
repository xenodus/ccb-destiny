<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App;
use DB;
use Stripe;
use Illuminate\Http\Request;

class StoreController extends Controller
{
  private $test_mode = false;

  public function stripeWebhook(Request $request) {

    Stripe\Stripe::setApiKey( env( $this->test_mode?'TEST_STRIPE_SECRET':'STRIPE_SECRET' ) );
    $endpoint_secret = env( $this->test_mode?'TEST_STRIPE_WEBHOOK_SECRET':'STRIPE_WEBHOOK_SECRET' );

    $payload = $request->getContent();
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;

    try {
      $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
      );
    } catch(\UnexpectedValueException $e) {
      // Invalid payload
      http_response_code(400); // PHP 5.4 or greater
      exit();
    } catch(\Stripe\Error\SignatureVerification $e) {
      // Invalid signature
      http_response_code(400); // PHP 5.4 or greater
      exit();
    }

    // Handle the checkout.session.completed event
    if ($event->type == 'checkout.session.completed') {
      $session = $event->data->object;

      // Fulfill the purchase...

      // Hard code to product id 1
      if( $session->client_reference_id ) {
        $product = App\Classes\Product::find(1);

        // Purchase Record
        $product_purchase = new \App\Classes\Product_Purchase();
        $product_purchase->product_name = $product->name;
        $product_purchase->product_id = $product->id;
        $product_purchase->pre_inventory = $product->inventory;
        $product_purchase->post_inventory = $product->inventory > 0 ? $product->inventory - 1 : 0;
        $product_purchase->date_added = date('Y-m-d H:i:s');
        // $product_purchase->ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        $product_purchase->save();

        if( $product->inventory > 0 ) $product->inventory = $product->inventory - 1;

        $product->save();
      }
    }

    http_response_code(200); // PHP 5.4 or greater
  }

  public function cos($slug='') {

    $product = App\Classes\Product::find(1);

    Stripe\Stripe::setApiKey( env( $this->test_mode?'TEST_STRIPE_SECRET':'STRIPE_SECRET' ) );
    $data['stripe_key'] = env( $this->test_mode?'TEST_STRIPE_KEY':'STRIPE_KEY' );

    $data['session'] = Stripe\Checkout\Session::create([
      'payment_method_types' => ['card'],
      'line_items' => [[
        'name' => $product->name,
        'description' => $product->description,
        'images' => [$product->image],
        'amount' => $product->price,
        'currency' => 'usd',
        'quantity' => 1,
      ]],
      'client_reference_id' => $product->id,
      'success_url' => route('product_purchase_cos_success', [1]),
      'cancel_url' => route('product_purchase_cos_failure', [1]),
    ]);

    $data['site_title'] = $product->name;
    $data['active_page'] = 'store';

    $data['site_image'] = $product->image;
    $data['site_description'] = $product->description;
    $data['success_url'] = route('product_purchase_cos_success', [1]);
    $data['cancelled_url'] = route('product_purchase_cos_failure', [1]);
    $data['inventory'] = $product->inventory;
    $data['sku'] = $product->sku;

    if( $slug != str_slug($data['site_title']) )
      return redirect()->route('product_cos_code', [str_slug($data['site_title'])]);

    return view('store.cos', $data);
  }

  public function success($id) {

    $data['product'] = \App\Classes\Product::find(1);
    $data['site_title'] = $data['product']->name;
    $data['active_page'] = 'store';

    return view('store.success', $data);
  }

  public function failure($id) {
    $product = \App\Classes\Product::find($id);

    if($id==1)
      return redirect()->route('product_cos_code', [str_slug($product->name)]);
    else
      return redirect()->route('product_cos_code', [str_slug($product->name)]);
  }
}