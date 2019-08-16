@extends('layouts.template')

@section('body')
<section class="stats text-center container-fluid mb-4">

	<div class="mt-4">
  	@include('stats.breadcrumbs', ['nav_link' => '/stats/raid', 'nav_name' => 'Raid Completions'])
  	<h1 class="text-yellow text-left">Raid Completion Stats</h1>
	</div>

  <div class="loader"></div>
  <div class="loader-text">Fetching Members...</div>

  <div class="form-group filter-container text-left mt-4" style="display: none;">
    <label for="nameFilter" class="d-md-inline-block">Filter by name</label>
    <input type="text" class="form-control form-control-sm d-md-inline-block ml-0 ml-md-1" id="nameFilter" style="max-width: 360px;">
  </div>

  <div class="stats-container mt-1"></div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/stats.css') }}"/>
@endsection

@section('footer')
<script src="{{ mix('/js/compiled/raid_stats.js') }}"></script>
@endsection