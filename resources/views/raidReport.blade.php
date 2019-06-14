@extends('layouts.template')

@section('body')
<section id="outbreak-solution" class="text-center container-fluid mt-4 mb-4">
  <div class="p-1">
    <h1 class="text-yellow mb-1">Raid.Report</h1>
    <div class="text-left mb-1"><small>Source: <a href="https://raid.report/pc/{{$memberID}}" target="_blank">https://raid.report/pc/{{$memberID}}</a></small></div>
  </div>
  <div class="p-1">
    <iframe src="https://raid.report/pc/{{$memberID}}"></iframe>
  </div>
</section>
@endsection