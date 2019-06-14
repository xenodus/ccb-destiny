@extends('layouts.template')

@section('body')
<section id="product-container" class="text-center container mt-4 mb-4">

  <div class="text-left bg-white text-danger mb-3">
    <div id="error-message" class="m-2"></div>
  </div>

  <div class="row">
    <div class="col-md-3">
      <div class="row">
        <div class="col-md-12">
          <div class="mb-2">
            <a href="/images/products/cos_jacket.jpg" data-lightbox="Crown of Sorrows Raid Jacket Discount Code">
              <img src="/images/products/cos_jacket.jpg" class="img-fluid"/>
            </a>
          </div>
        </div>
      </div>
      <div class="row no-gutters">
        <div class="col-3 col-md-4 pr-1">
          <a href="/images/products/cos_jacket_inside.jpg" data-lightbox="Crown of Sorrows Raid Jacket Discount Code">
            <img src="/images/products/cos_jacket_inside.jpg" class="img-fluid border border-white"/>
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-9 text-left">
      <h1 class="text-yellow mb-1 mt-3 mt-md-0">Crown of Sorrow Raid Jacket Discount Code</h1>
      <div class="product-meta">
        <div class="product-price mb-2">$24.99</div>
        <div class="product-description mb-4">
          <p>
            1 x Crown of Sorrow Raid Jacket <strong><u>Discount Code</u></strong> to be used at <a href="https://bungiestore.com/products/preorder-bungie-rewards-destiny-2-forsaken-crown-of-sorrow-raid-jacket" target="_blank">Bungie Store</a>.
          </p>
          <p>Discount codes were only made available to players who completed the Destiny 2: Season of Opulence Crown of Sorrow Raid before the first reset on 11th June 2019.</p>
          <p>Discount code must be used on Bungie Store by <u>18th June 2019 11:59 PM PDT</u>.</p>
          <p>Discount code will be e-mailed to you within 24 hours of purchase.</p>
          <p>Have a question? Drop by our <a href="https://discord.gg/5u2RYc9" target="_blank">Discord</a> or send us an email at <a href="mailto:ccboys.enquiries@gmail.com" target="_blank">ccboys.enquiries@gmail.com</a></p>
        </div>
        <div class="product-cta">
          @if( $inventory == 0 )
          <button role="link" class="btn btn-danger" disabled>Sold Out</button>
          @else
          <div class="d-flex align-items-center">
            <button id="checkout-button" role="link" class="btn btn-primary">Buy Now</button>
            <div class="product-inventory ml-3 text-yellow">
              {{$inventory}} Code{{$inventory>1?'s':''}} Left
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('header')
<script src="https://js.stripe.com/v3"></script>
<link rel="stylesheet" href="{{ mix('/css/compiled/store.css') }}"/>
@endsection

@section('footer')
<script>
  var stripe = Stripe('{{ $stripe_key }}');

  var checkoutButton = document.getElementById('checkout-button');
  checkoutButton.addEventListener('click', function () {

    stripe.redirectToCheckout({
      sessionId: '{{$session->id}}'
    })
    .then(function (result) {
      if (result.error) {
        var displayError = document.getElementById('error-message');
        displayError.textContent = result.error.message;
      }
    });
  });
</script>
@endsection