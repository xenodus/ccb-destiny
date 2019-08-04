@extends('layouts.template')

@section('body')
<section id="exotic-collection" class="container text-center mb-4">
  <div class="mt-4 mb-4">
    @include('clan.nav')
  </div>
  <div class="loader"></div>
  <div class="loader-text mb-4">Fetching Members...</div>

  <div class="form-group filter-container text-left" style="display: none;">
    <label for="nameFilter" class="d-md-inline-block">Filter by name</label>
    <input type="text" class="form-control d-md-inline-block ml-0 ml-md-1" id="nameFilter" style="max-width: 360px;">
  </div>
  <div class="stats-container"></div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/clan.css') }}"/>
@endsection

@section('footer')
<script src="{{ mix('/js/compiled/exotics.js') }}"></script>
@endsection