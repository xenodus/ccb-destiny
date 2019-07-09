@extends('layouts.template')

@section('body')
<section id="roster" class="container text-center mb-4">
  <div class="mt-4 mb-4">
    @include('clan.nav')
  </div>
  <div class="loader"></div>
  <div class="loader-text mb-4">Fetching Members...</div>

  <div>
    <div class="row">
      <div class="col-md-12">
        <div id="roster-container"></div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/clan.css') }}"/>
@endsection

@section('footer')
<script src="{{ mix('/js/compiled/roster.js') }}"></script>
@endsection