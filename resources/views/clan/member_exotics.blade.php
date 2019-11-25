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
            <span itemprop="name">Uncollected Exotics</span>
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

    <h1 class="text-yellow text-left">Uncollected Exotics</h1>
  </div>
  <div class="loader"></div>
  <div class="loader-text mb-4">Fetching Members...</div>

  <div class="form-group filter-container text-left" style="display: none;">
    <label for="nameFilter" class="d-md-inline-block">Filter by name</label>
    <input type="text" class="form-control form-control-sm d-md-inline-block ml-0 ml-md-1" id="nameFilter" style="max-width: 360px;">
  </div>
  <div class="stats-container"></div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/clan.css') }}"/>
<style type="text/css">
  .tabulator-row.tabulator-selectable:hover {
    background-color: inherit;
  }
  .tabulator-row.tabulator-row-even {
    background-color: #eee;
  }
  .tabulator-row.tabulator-row-even:hover {
    background-color: #eee;
  }
  .exotic-item {
    font-size: 0.8rem;
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