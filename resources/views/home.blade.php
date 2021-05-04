@extends('layouts.template')

@section('body')
<section class="about-us text-center container-fluid mt-2 border-bottom border-dark">
  <div class="container mb-4 py-5" id="about-us-container">

    <div id="about-us-header" class="mb-3 text-yellow d-flex justify-content-start align-items-center standard-header">
      <div id="about-us-header-icon"></div>
      <h1>About Us</h1>
    </div>
    <div id="about-us-description" class="text-justify">
      <p>Greetings from the Carrot Cake for Breakfast [CCB] clan in Destiny 2.</p>

      <p>We are a laid-back group of guardians from <span class="flag-icon flag-icon-sg"></span> Singapore. Most of us are PvE oriented and raid on a weekly basis. While the majority of us are working adults, we also have students and retirees among our ranks.</p>

      <p>All fellow Singlish speaking guardians are welcome to join us in Shadowkeep and Beyond Light. Our only criteria is that all members treat each other with respect. <a href="{{ env('DISCORD_LINK') }}" target="_blank">Come say 👋 hi to us in Discord!</a></p>
    </div>

    <div class="row mt-5">
      <div class="col-md-4">
        <div class="border border-yellow traits-box p-3">
          <div class="traits-title pl-2 pr-2"><i class="ra ra-hammer-drop mr-1"></i>Playstyle</div>
          Raid-centric PVE
        </div>
      </div>
      <div class="col-md-4">
        <div class="border border-yellow traits-box p-3">
          <div class="traits-title pl-2 pr-2"><i class="ra ra-heart-bottle mr-1"></i>Age</div>
          21+
        </div>
      </div>
      <div class="col-md-4">
        <div class="border border-yellow traits-box p-3">
          <div class="traits-title pl-2 pr-2"><i class="ra ra-clockwork mr-1"></i>Timezone</div>
          GMT+8
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-md-4">
        <div class="border border-yellow traits-box p-3">
          <div class="traits-title pl-2 pr-2"><i class="ra ra-demolish mr-1"></i>Raids Conquered</div>
          {{ number_format($raids_completed) }}
        </div>
      </div>
      <div class="col-md-4">
        <div class="border border-yellow traits-box p-3">
          <div class="traits-title pl-2 pr-2"><i class="ra ra-hydra mr-1"></i>Enemies Slain</div>
          {{ number_format($pve_kills) }}
        </div>
      </div>
      <div class="col-md-4">
        <div class="border border-yellow traits-box p-3">
          <div class="traits-title pl-2 pr-2"><i class="ra ra-double-team mr-1"></i>Members</div>
          {{ $clan_members_count }}
        </div>
      </div>
    </div>
  </div>
</section>

<section id="guides" class="text-center container-fluid border-bottom border-dark">
  <div class="container py-5">
    <div id="guides-header" class="mb-3 text-yellow d-flex justify-content-start align-items-center standard-header">
      <div id="latest-post-header-icon"></div>
      <div style="position: relative; bottom: 8px;">
        <h1>Latest Guides</h1>
      </div>
    </div>
    <div id="guides-item-container" class="row">
    </div>
  </div>

  <div class="loader"></div>
  <div class="loader-text mb-5">Fetching Guides...</div>
</section>

<section class="text-center container-fluid border-bottom border-dark">
  <div class="container py-3">
    <div class="overflow-hidden">
      @include('ads.horizontal')
    </div>
  </div>
</section>

<!--section id="news" class="text-center container-fluid mb-4">
  <div class="container py-5">
    <div id="news-header" class="mb-3 text-yellow d-flex justify-content-start align-items-center standard-header">
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
</section-->
@endsection

@section('footer')
<script src="{{ mix('/js/compiled/index.js') }}"></script>
@endsection