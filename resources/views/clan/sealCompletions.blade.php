@extends('layouts.template')

@section('body')
<section id="seal-completions" class="container-fluid text-center mb-4">
  <div class="my-4">
    @include('clan.breadcrumbs', ['nav_link' => '/clan/seals', 'nav_name' => 'Seal Completions'])
    <h1 class="text-yellow text-left">Seal Completions</h1>
  </div>
  <div class="loader"></div>
  <div class="loader-text mb-4">Fetching Members...</div>

  <div class="form-group filter-container text-left" style="display: none;">
    <label for="nameFilter" class="d-md-inline-block">Filter by name</label>
    <input type="text" class="form-control form-control-sm d-md-inline-block ml-0 ml-md-1" id="nameFilter" style="max-width: 360px;">
  </div>
  <div class="stats-container"></div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/clan.css') }}"/>
@endsection

@section('footer')
<script src="{{ mix('/js/compiled/seals.js') }}"></script>
@endsection