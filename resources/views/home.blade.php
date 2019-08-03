@extends('layouts.template')

@section('body')
<section class="about-us text-center container-fluid mt-2 border-bottom border-dark">
  <div class="container mb-4 pb-5 pt-5" id="about-us-container">
    <div id="about-us-header" class="mb-3 text-yellow d-flex justify-content-start align-items-center">
      <div id="about-us-header-icon"></div>
      <h1>About Us</h1>
    </div>
    <div id="about-us-description" class="text-justify">
      <p>{{ env('SITE_DESCRIPTION') }}</p>
      <p><a href="https://www.bungie.net/en-us/ClanV2?groupid=3717919" target="_blank">We're recruiting for PC / Battle.net / Steam!</a> We have an active & friendly discord, internal progression systems and 9+ raids/week; all experience levels welcome! <a href="{{ env('DISCORD_LINK') }}" target="_blank">Drop by our Discord server to say hi!</a></p>
      <!--p>We're on PC / Battle.net. Most of our activities and communications are held inside our <a href="{{ env('DISCORD_LINK') }}" target="_blank">Discord</a> server. Drop by to say hi!</p-->
    </div>

    <div class="row mt-5">
      <div class="col-md-4">
        <div class="border border-yellow traits-box p-3">
          <div class="traits-title pl-2 pr-2"><i class="ra ra-hammer-drop mr-1"></i>Playstyle</div>
          Raid-centric PVEVP
        </div>
      </div>
      <div class="col-md-4">
        <div class="border border-yellow traits-box p-3">
          <div class="traits-title pl-2 pr-2"><i class="ra ra-heart-bottle mr-1"></i>Age</div>
          18+
        </div>
      </div>
      <div class="col-md-4">
        <div class="border border-yellow traits-box p-3">
          <div class="traits-title pl-2 pr-2"><i class="ra ra-clockwork mr-1"></i>Timezone</div>
          GMT+8
        </div>
      </div>
    </div>
  </div>
</section>

<section id="weeklies" class="text-center container-fluid border-bottom border-dark">
  <div class="container pb-5 pt-5 mb-4">
    <div id="milestones-header" class="mb-3 text-yellow d-flex justify-content-start align-items-center">
      <div id="milestones-header-icon" class="mr-1"></div>
      <h1>Weeklies / Dailies</h1>
      <div class="ml-auto d-flex align-items-center mobile-hide">
        <small>Auto Refresh <span class="auto-refresh-timer" data-timer="30"></span></small>
        <div class="d-flex align-items-center ml-3 text-white">
          <div class="switch">
            <input type="radio" class="switch-input" name="view" value="1" id="autoRefreshOn" {{ \Cookie::get('autoRefreshMilestones') ? 'checked':''}}>
            <label for="autoRefreshOn" class="switch-label switch-label-off">ON</label>
            <input type="radio" class="switch-input" name="view" value="0" id="autoRefreshOff" {{ \Cookie::get('autoRefreshMilestones') ? '':'checked'}}>
            <label for="autoRefreshOff" class="switch-label switch-label-on">OFF</label>
            <span class="switch-selection"></span>
          </div>
        </div>
      </div>
    </div>
    <div id="weeklies-item-container" class="grid row"></div>
  </div>

  <div class="loader"></div>
  <div class="loader-text mb-5">Fetching Milestones...</div>
</section>

<section id="guides" class="text-center container-fluid mb-4 border-bottom border-dark">
  <div class="container pb-5 pt-5">
    <div id="guides-header" class="mb-3 text-yellow d-flex justify-content-start align-items-center">
      <div id="latest-post-header-icon"></div>
      <div style="position: relative; bottom: 8px;" class="ml-2">
        <h1>Latest Guides</h1>
      </div>
    </div>
    <div id="guides-item-container" class="row">
    </div>
  </div>

  <div class="loader"></div>
  <div class="loader-text mb-5">Fetching Guides...</div>
</section>

<section id="news" class="text-center container-fluid mb-4">
  <div class="container pb-5 pt-5">
    <div id="news-header" class="mb-3 text-yellow d-flex justify-content-start align-items-center">
      <div id="news-header-icon"></div>
      <div style="position: relative; bottom: 8px;" class="ml-2">
        <h1>Latest News</h1>
        <div style="position: absolute; font-size: 0.6rem;" class="text-left w-100">- Powered by <a href="//newsapi.org" style="text-decoration: underline;" target="_blank">NewsAPI.org</a> -</div>
      </div>
    </div>
    <div id="news-item-container" class="row">
    </div>
  </div>

  <div class="loader"></div>
  <div class="loader-text mb-5">Fetching News...</div>
</section>
@endsection

@section('footer')
<script type="text/javascript">
  ccbNS["autoRefreshMilestones"] = {{ \Cookie::get('autoRefreshMilestones') ?? 0 }};
</script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script src="{{ mix('/js/compiled/index.js') }}"></script>
@endsection