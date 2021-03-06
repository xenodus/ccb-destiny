@extends('layouts.template')

@section('body')
<section id="roster" class="container-fluid text-center mb-4">
  <div class="my-4">
    @include('clan.breadcrumbs', ['nav_link' => '/clan/roster', 'nav_name' => 'Roster'])
    <h1 class="text-yellow text-left">Roster</h1>
  </div>
  <div class="loader"></div>
  <div class="loader-text mb-4">Fetching Members...</div>

  <div class="form-group filter-container text-left mt-4" style="display: none;">
    <label for="nameFilter" class="d-md-inline-block">Filter by name</label>
    <input type="text" class="form-control form-control-sm d-md-inline-block ml-0 ml-md-1" id="nameFilter" style="max-width: 360px;">
  </div>

  <div>
    <div class="row">
      <div class="col-md-12">
        <div id="roster-container"></div>
      </div>
    </div>
  </div>

  <div class="overflow-hidden">
    @include('ads.horizontal')
  </div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/clan.css') }}"/>
@endsection

@section('footer')
<!--script src="/js/clan/roster.js?<?=time()?>"></script-->
<script src="{{ mix('/js/compiled/roster.js') }}"></script>
@endsection