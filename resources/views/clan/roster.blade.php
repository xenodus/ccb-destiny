@extends('layouts.template')

@section('body')
<section id="roster" class="container-fluid text-center mb-4">
  <div class="my-4">
    @include('clan.breadcrumbs', ['nav_link' => '/clan/roster', 'nav_name' => 'Roster'])
    <h1 class="text-yellow text-left">Roster</h1>
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