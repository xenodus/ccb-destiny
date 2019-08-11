@extends('layouts.template')

@section('body')
<section class="stats text-center container-fluid mb-4">

	<div class="mt-4">
  	@include('stats.breadcrumbs', ['nav_link' => '/stats/pvp', 'nav_name' => 'PvP'])
  	<h1 class="text-yellow text-left">PvP Stats</h1>
	</div>

  <div class="loader"></div>
  <div class="loader-text">Fetching Members...</div>

  <div class="stats-container mt-4"></div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/stats.css') }}"/>
@endsection

@section('footer')
<script src="{{ mix('/js/compiled/pvp_stats.js') }}"></script>
@endsection