@extends('layouts.template')

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/post.css') }}"/>
@endsection

@section('body')
<section id="guide-listing" class="text-center container mt-5 mb-5">
  <div>
    <nav aria-label="breadcrumb" style="display: none;">
      <ol class="breadcrumb bg-transparent pl-0" vocab="https://schema.org/" typeof="BreadcrumbList">
        <li class="breadcrumb-item" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage"
              href="{{ route('guide_index') }}">
            <span property="name">Guides</span></a>
          <meta property="position" content="1">
        </li>
      </ol>
    </nav>
  </div>
  <div class="row mb-3 categories-img">
    <div class="col-md-4 text-center">
      <a href="{{ route('guide_category', ['slug' => $destiny_category->term->slug, 'id' => $destiny_category->term_taxonomy_id]) }}">
        <div class="d-flex flex-column align-items-center justify-content-center item item-destiny mb-3 p-2">
          <div class="item-overlay"></div>
          <div class="item-name">Destiny 2</div>
        </div>
      </a>
    </div>
    <div class="col-md-4 text-center">
      <a href="{{ route('guide_category', ['slug' => $magic_category->term->slug, 'id' => $magic_category->term_taxonomy_id]) }}">
        <div class="d-flex align-items-center justify-content-center item item-mtg mb-3 p-2">
          <div class="item-overlay"></div>
          <div class="item-name">Magic: The Gathering Arena</div>
        </div>
      </a>
    </div>
    <div class="col-md-4 text-center">
      <a href="{{ route('guide_category', ['slug' => $division_category->term->slug, 'id' => $division_category->term_taxonomy_id]) }}">
        <div class="d-flex align-items-center justify-content-center item item-division mb-3 p-2">
          <div class="item-overlay"></div>
          <div class="item-name">The Division 2</div>
        </div>
      </a>
    </div>
  </div>
  <div class="mb-4 text-yellow d-flex justify-content-start align-items-center">
    <div id="latest-post-header-icon"></div>
    <h1>Latest Posts</h1>
  </div>
  <div class="row">
    <div class="col-md-9">
      <div class="row">
        @foreach($posts as $post)
          @include('guides.postItemHorizontal')
        @endforeach
      </div>
    </div>
    <div class="col-md-3 col-sm-12">
      @include('guides.widgets.sidebarNav')
      @include('guides.widgets.mtgTopDecks')
    </div>
  </div>
  <div class="pagination-container text-center mt-2">
    @if( !$posts->onFirstPage() )
      <a href="{{ $posts->previousPageUrl() }}" class="mr-2"> <i class="fas fa-angle-double-left"></i> Prev Page</a>
    @endif
    @if( $posts->hasMorePages()  )
      <a href="{{ $posts->nextPageUrl() }}" class="ml-2">Next Page <i class="fas fa-angle-double-right"></i></a>
    @endif
  </div>
</section>
@endsection

@section('footer')
<script type="text/javascript">
  var news_listing_category_slug = "{{$top_category->term->slug ?? ''}}";
</script>
<script src="{{ mix('/js/compiled/post.js') }}"></script>
@endsection