@extends('layouts.template')

@section('body')
<section id="raid-lockouts" class="container text-center mb-4">
  <div class="my-4">
    @include('clan.breadcrumbs', ['nav_link' => '/clan/lockouts', 'nav_name' => 'Weekly Raid Lockouts'])
    <h1 class="text-yellow text-left">Weekly Raid Lockouts</h1>
  </div>
  <div class="loader"></div>
  <div class="loader-text mb-4">Fetching Members...</div>

  <div>
    <div class="row">
      <div class="col-md-3">
        <div id="sub-menu" class="mb-md-5 mb-4 sticky-top pt-md-4">
          <ul class="nav flex-column"></ul>
        </div>
      </div>
      <div class="col-md-9">
        <div class="stats-container row"></div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/clan.css') }}"/>
@endsection

@section('footer')
<script src="{{ mix('/js/compiled/lockouts.js') }}"></script>
@endsection