@extends('layouts.template')

@section('body')
<section class="stats text-center container-fluid mb-4">

  @include('stats.submenu')

  <div class="loader"></div>
  <div class="loader-text">Fetching Members...</div>

  <div class="container-fluid stats-container mt-4"></div>
</section>
@endsection

@section('footer')
<script src="{{ mix('/js/compiled/pvp_stats.js') }}"></script>
@endsection