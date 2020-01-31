@extends('layouts.template')

@section('body')
<section id="exotic-collection" class="container-fluid text-center mb-4">
  <div class="my-4">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent pl-0 py-0 mb-3" itemscope itemtype="https://schema.org/BreadcrumbList">
        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" itemtype="https://schema.org/WebPage" href="/clan">
            <span itemprop="name">Clan</span>
          </a>
          <meta itemprop="position" content="1">
        </li>

        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" itemtype="https://schema.org/WebPage" href="/clan/exotics">
            <span itemprop="name">Exotic Collection</span>
          </a>
          <meta itemprop="position" content="2">
        </li>

        <li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" itemtype="https://schema.org/WebPage" href="/clan/exotics/{{$member->id}}/{{$member->display_name}}">
            <span itemprop="name">{{$member->display_name}}</span>
          </a>
          <meta itemprop="position" content="3">
        </li>
      </ol>
    </nav>

    <h1 class="text-yellow text-left">Exotic Collection</h1>
  </div>
  <div class="loader"></div>
  <div class="loader-text mb-4">Fetching Members...</div>

  <div class="exotic-collection-container">
    <div class="summary">
      <div class="row">
        <div class="col-md-3 mb-md-0 mb-3">
          <a href="#weapons">
            <div class="summary-item">
              <img src="https://www.bungie.net/common/destiny2_content/icons/98277564267705162c0eac005b9d514e.png"/>
              <div class="summary-title summary-title-weapons">Weapons</div>
              <div class="summary-collected-weapons"></div>
            </div>
          </a>
        </div>
        <div class="col-md-3 mb-md-0 mb-3">
          <a href="#warlock">
            <div class="summary-item">
              <img src="https://www.bungie.net/common/destiny2_content/icons/bf7b2848d2f5fbebbf350d418b8ec148.png"/>
              <div class="summary-title summary-title-warlock">Warlock</div>
              <div class="summary-collected-warlock"></div>
            </div>
          </a>
        </div>
        <div class="col-md-3 mb-md-0 mb-3">
          <a href="#titan">
            <div class="summary-item">
              <img src="https://www.bungie.net/common/destiny2_content/icons/8956751663b4394cd41076f93d2dd0d6.png"/>
              <div class="summary-title summary-title-titan">Titan</div>
              <div class="summary-collected-titan"></div>
            </div>
          </a>
        </div>
        <div class="col-md-3 mb-md-0 mb-3">
          <a href="#hunter">
            <div class="summary-item">
              <img src="https://www.bungie.net/common/destiny2_content/icons/e7324e8c29c5314b8bce166ff167859d.png"/>
              <div class="summary-title summary-title-hunter">Hunter</div>
              <div class="summary-collected-hunter"></div>
            </div>
          </a>
        </div>
      </div>
    </div>

    <div id="weapons" class="exotic-weapons pt-md-5 pt-3">
      <div class="d-flex justify-content-between mb-md-3 mb-2">
        <div>
          <h4 class="text-yellow text-left">Weapons</h4>
        </div>
        <div class="uncollected-legend d-flex align-items-center">
          <div class="uncollected-bar"></div>
          <div class="ml-2">Uncollected</div>
        </div>
      </div>
      <div class="exotic-weapons-container row"></div>
    </div>

    <div id="warlock" class="exotic-warlock pt-md-5 pt-3">
      <div class="d-flex justify-content-between mb-md-3 mb-2">
        <div>
          <h4 class="text-yellow text-left">Warlock</h4>
        </div>
        <div class="uncollected-legend d-flex align-items-center">
          <div class="uncollected-bar"></div>
          <div class="ml-2">Uncollected</div>
        </div>
      </div>
      <div class="exotic-warlock-container row"></div>
    </div>

    <div id="titan" class="exotic-titan pt-md-5 pt-3">
      <div class="d-flex justify-content-between mb-md-3 mb-2">
        <div>
          <h4 class="text-yellow text-left">Titan</h4>
        </div>
        <div class="uncollected-legend d-flex align-items-center">
          <div class="uncollected-bar"></div>
          <div class="ml-2">Uncollected</div>
        </div>
      </div>
      <div class="exotic-titan-container row"></div>
    </div>

    <div id="hunter" class="exotic-hunter pt-md-5 pt-3">
      <div class="d-flex justify-content-between mb-md-3 mb-2">
        <div>
          <h4 class="text-yellow text-left">Hunter</h4>
        </div>
        <div class="uncollected-legend d-flex align-items-center">
          <div class="uncollected-bar"></div>
          <div class="ml-2">Uncollected</div>
        </div>
      </div>
      <div class="exotic-hunter-container row"></div>
    </div>
  </div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/clan.css') }}"/>
<style type="text/css">
  .exotic-collection-container {
    display: none;
  }
  .summary a,
  .summary a:hover,
  .summary a:active,
  .summary a:focus {
    color: #fff;
    text-decoration: none;
  }
  .summary-item {
    background: rgba(255,255,255,0.1);
    padding: 5px 5px 15px 5px;
  }
  .summary-item img {
    height: 50px;
  }
  .exotic-item {
    background: rgba(255,255,255,0.1);
    color: #fff;
    font-size: 12px;
  }
  .exotic-item img {
    width: 40px;
    height: 40px;
    margin-right: 15px;
  }
  .missing {
    border-right: 4px solid red;
  }
  .uncollected-legend {
    font-size: 12px;
  }
  .uncollected-bar {
    background: red;
    height: 35px;
    width: 4px;
  }
</style>
@endsection

@section('footer')
<script>
var member = JSON.parse('{!! $member->toJson() !!}');
</script>
<script src="{{ mix('/js/compiled/member_exotics.js') }}"></script>
<!--script src="/js/clan/member_exotics.js?<?=time()?>"></script-->
@endsection