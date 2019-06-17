@extends('layouts.template')

@section('body')
<section id="seal-completions" class="container text-center mb-4">
  <div class="mt-4 mb-4">
    @include('clan.nav')
    <!--h1 class="text-yellow text-left">Seal Completions</h1-->
  </div>
  <div class="loader"></div>
  <div class="loader-text mb-4">Fetching Members...</div>

  <div class="stats-container"></div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/clan.css') }}"/>
@endsection

@section('footer')
<!--script src="{{ mix('/js/compiled/seals.js') }}"></script-->
<script src="/js/clan/seals.js?<?=time()?>"></script>
@endsection