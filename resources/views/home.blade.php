@extends('layouts.template')

@section('body')
<section class="about-us text-center container-fluid mt-2 mb-4 border-bottom border-dark">
  <div class="container mb-4 pb-4 pt-4" id="about-us-container">
    <div id="about-us-header" class="mb-3 text-yellow d-flex justify-content-start align-items-center">
      <div id="about-us-header-icon" class="animated rollIn"></div>
      <h1>About Us</h1>
    </div>
    <div id="about-us-description" class="text-justify">
      <p>We're the Chilli Crab Boys [CCB]. The pinnacle of Singaporean cuisine and a pun on a certain singlish expletive. We're a tight knit group of guardians from Singapore & Australia with a common goal of helping out one another while having fun in the process. Aside from Destiny 2, we're also active in The Division 2 and Magic: The Gathering Arena.</p>
      <p>Most of our activities and communications are held inside our <a href="https://discord.gg/Xx6DVJq" target="_blank">Discord server</a> so do drop by to say hi!</p>
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

<section id="weeklies" class="text-center container-fluid mb-4">
  <div class="container pb-2 pt-2">
    <div id="milestones-header" class="mb-3 text-yellow d-flex justify-content-start align-items-center">
      <div id="milestones-header-icon" class="animated rollIn"></div>
      <h1>Weeklies / Dailies</h1>
    </div>
    <div id="weeklies-item-container" class="grid row">
      <div class="grid-sizer"></div>
      <div class="gutter-sizer"></div>
    </div>
  </div>

  <div class="loader"></div>
  <div class="loader-text mb-4">Fetching Milestones...</div>
</section>
@endsection

@section('footer')
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script src="{{ mix('/js/compiled/index.js') }}"></script>
@endsection