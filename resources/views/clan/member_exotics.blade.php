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
              <div class="weapons-progress-bar mt-2"></div>
            </div>
          </a>
        </div>
        <div class="col-md-3 mb-md-0 mb-3">
          <a href="#warlock">
            <div class="summary-item">
              <img src="https://www.bungie.net/common/destiny2_content/icons/bf7b2848d2f5fbebbf350d418b8ec148.png"/>
              <div class="summary-title summary-title-warlock">Warlock</div>
              <div class="summary-collected-warlock"></div>
              <div class="warlock-progress-bar mt-2"></div>
            </div>
          </a>
        </div>
        <div class="col-md-3 mb-md-0 mb-3">
          <a href="#titan">
            <div class="summary-item">
              <img src="https://www.bungie.net/common/destiny2_content/icons/8956751663b4394cd41076f93d2dd0d6.png"/>
              <div class="summary-title summary-title-titan">Titan</div>
              <div class="summary-collected-titan"></div>
              <div class="titan-progress-bar mt-2"></div>
            </div>
          </a>
        </div>
        <div class="col-md-3 mb-md-0 mb-3">
          <a href="#hunter">
            <div class="summary-item">
              <img src="https://www.bungie.net/common/destiny2_content/icons/e7324e8c29c5314b8bce166ff167859d.png"/>
              <div class="summary-title summary-title-hunter">Hunter</div>
              <div class="summary-collected-hunter"></div>
              <div class="hunter-progress-bar mt-2"></div>
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
          <div class="mr-2 text-right">Uncollected denoted by</div>
          <div class="uncollected-bar"></div>
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
          <div class="mr-2 text-right">Uncollected denoted by</div>
          <div class="uncollected-bar"></div>
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
          <div class="mr-2 text-right">Uncollected denoted by</div>
          <div class="uncollected-bar"></div>
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
          <div class="mr-2 text-right">Uncollected denoted by</div>
          <div class="uncollected-bar"></div>
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
    background: rgba(0,0,0,0.3);
    padding: 5px 5px 15px 5px;
  }
  .summary-item img {
    height: 50px;
  }
  .exotic-item {
    background: rgba(0,0,0,0.7);
    color: #fff;
    font-size: 12px;
    line-height: 18px;
    letter-spacing: 1px;
  }
  a.exotic-item-guide {
    text-decoration: none;
  }
  .exotic-item img {
    max-width: 60px;
    height: auto;
  }
  .exotic-item-guide {
    font-size: 12px;
  }
  .missing img {
    opacity: 0.2;
  }
  .missing {
    border-right: 5px solid red;
  }
  .uncollected-legend {
    font-size: 12px;
  }
  .uncollected-bar {
    background: red;
    height: 35px;
    width: 5px;
  }
</style>
@endsection

@section('footer')
<script>
var member = JSON.parse('{!! $member->toJson() !!}');
</script>
<!--script src="{{ mix('/js/compiled/member_exotics.js') }}"></script-->
<script src="/js/clan/member_exotics.js?<?=time()?>"></script>
@endsection