@extends('layouts.template')

@section('body')
<section id="exotic-collection" class="container-fluid text-center mb-4">
  <div class="my-4">
    @include('clan.breadcrumbs', ['nav_link' => '/clan/exotics', 'nav_name' => 'Uncollected Exotics'])
    <h1 class="text-yellow text-left">Uncollected Exotics</h1>
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
<style type="text/css">
  .tabulator-row.tabulator-selectable:hover {
    background-color: inherit;
  }
  .tabulator-row.tabulator-row-even {
    background-color: #eee;
  }
  .tabulator-row.tabulator-row-even:hover {
    background-color: #eee;
  }
  .exotic-item {
    font-size: 0.8rem;
  }
</style>
@endsection

@section('footer')
<script src="{{ mix('/js/compiled/exotics.js') }}"></script>
@endsection