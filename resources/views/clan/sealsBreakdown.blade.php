@extends('layouts.template')

@section('body')
<section id="seal-completions" class="container text-center mb-4">
  <div class="my-4"></div>
  <div class="loader"></div>
  <div class="loader-text mb-4">Fetching Triumphs...</div>

  <div>
    <div class="row">
      <div class="col-md-12">
        <div id="back-nav" class="text-left text-yellow mb-2" style="display: none;">
          <a href="{{ route('clan_seal_progression') }}">
            <i class="fas fa-angle-double-left mr-1"></i>Back to Clan Seal Completions
          </a>
        </div>
        <h1 id="member-name" class="text-yellow text-left" style="font-size: 2rem; display: none;">{{ $member->display_name }}</h1>
      </div>
      <div class="col-md-3">
        <div id="sub-menu" class="mb-5 sticky-top pt-md-4">
          <ul class="nav flex-column"></ul>
        </div>
      </div>
      <div class="col-md-9">
        <div class="stats-container row"></div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/clan.css') }}"/>
@endsection

@section('footer')
<script type="text/javascript">
  ccbNS["member_id"] = '{{ $member_id }}';
  ccbNS["member_platform_type"] = '{{ $member->membershipType }}';
</script>
<!--script src="{{ mix('/js/compiled/sealProgression.js') }}"></script-->
<script src="/js/clan/sealProgression.js?<?=time()?>"></script>
@endsection