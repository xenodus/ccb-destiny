@extends('layouts.template')

@section('body')
<section id="product-container" class="text-center container mt-4 mb-4">
  <div class="row">
    <div class="col-md-12">
      <h1 class="text-yellow mb-4 mt-3 mt-md-0 text-center">Order Submitted</h1>
      <p>Thank you for your purchase. We'll process your order as soon as possible!<p>
      <p>Have a question? Drop by our <a href="https://discord.gg/5u2RYc9" target="_blank">Discord</a> or send us an email at <a href="mailto:ccboys.enquiries@gmail.com" target="_blank">ccboys.enquiries@gmail.com</a></p>
      <p>
        <a href="{{route('product_cos_code', [str_slug($product->name)])}}"><i class="fas fa-angle-double-left"></i> Back</a>
      </p>
    </div>
  </div>

  <div id="error-message"></div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="/css/store.css?<?=time()?>"/>
@endsection