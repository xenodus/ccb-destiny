@extends('layouts.template')

@section('body')
<section id="weeklies" class="text-center container-fluid border-bottom border-dark">
  <div class="container py-5 mb-4">
    <div id="milestones-header" class="mb-5 text-yellow d-flex justify-content-start align-items-center">
      <!--div id="milestones-header-icon" class="mr-1"></div-->
      <div style="position: relative; bottom: 8px;">
        <h1>Weeklies / Dailies</h1>
        <div style="position: absolute; font-size: 0.6rem;" class="text-left w-100">- Powered by <a href="https://github.com/Bungie-net/api" style="text-decoration: underline;" target="_blank">Bungie.Net API</a> -</div>
      </div>
    </div>

    <div id="weeklies-raid-item-container-wrapper">
      <div class="weeklies-sub-header text-left d-flex align-items-center">
        <img src="https://www.bungie.net/common/destiny2_content/icons/DestinyActivityModeDefinition_bfe80e3dafe6686a9dc42df0606bdc9b.png" style="height: 45px; margin-left: -5px;">
        Raid
      </div>
      <div id="weeklies-raid-item-container" class="grid row"></div>
    </div>

    <div id="weeklies-vanguard-item-container-wrapper">
      <div class="weeklies-sub-header text-left d-flex align-items-center">
        <img src="https://bungie.net/common/destiny2_content/icons/DestinyPresentationNodeDefinition_f45a98686356e678499ee79cef5e8e02.png" style="height: 40px; margin-right: 10px;">
        Vanguard / World PvE
      </div>
      <div id="weeklies-vanguard-item-container" class="grid row"></div>
    </div>

    <div id="weeklies-gambit-item-container-wrapper">
      <div class="weeklies-sub-header text-left d-flex align-items-center">
        <img src="https://bungie.net/common/destiny2_content/icons/3664df928a70aa37fc65831b7ba5cb67.png" style="height: 40px; margin-right: 5px;">
        Gambit
      </div>
      <div id="weeklies-gambit-item-container" class="grid row"></div>
    </div>

    <div id="weeklies-crucible-item-container-wrapper">
      <div class="weeklies-sub-header text-left d-flex align-items-center">
        <img src="https://bungie.net/common/destiny2_content/icons/DestinyMilestoneDefinition_6e3e2457fd1f7a9df5c491213bcaf133.png" style="height: 40px; margin-right: 5px;">
        Crucible
      </div>
      <div id="weeklies-crucible-item-container" class="grid row"></div>
    </div>

    <div id="weeklies-vendors-item-container-wrapper">
      <div class="weeklies-sub-header text-left d-flex align-items-center">
        <img src="https://bungie.net/common/destiny2_content/icons/8e5c144a90a5b6ec351daf875096bf5b.png" style="height: 40px; margin-left: -5px; margin-right: 5px;">
        Vendors
      </div>
      <div id="weeklies-vendors-item-container" class="grid row"></div>
    </div>
  </div>

  <style>
    #weeklies-raid-item-container-wrapper,
    #weeklies-vanguard-item-container-wrapper,
    #weeklies-gambit-item-container-wrapper,
    #weeklies-crucible-item-container-wrapper,
    #weeklies-vendors-item-container-wrapper {
      margin-bottom: 30px;
    }
    .weeklies-sub-header {
      font-size: 1.3rem;
      margin-bottom: 15px;
      letter-spacing: 1px;
    }
  </style>

  <div class="loader"></div>
  <div class="loader-text mb-5">Fetching Milestones...</div>
</section>
@endsection

@section('footer')
<script src="{{ mix('/js/compiled/weeklies.js') }}"></script>
<!--script src="../node_modules/masonry-layout/dist/masonry.pkgd.min.js?<?=time()?>"></script>
<script src="/js/weeklies/weeklies.js?<?=time()?>"></script-->
@endsection