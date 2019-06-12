@extends('layouts.template')

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/post.css') }}"/>
@endsection

@section('body')
<section id="guide-listing" class="text-center container mt-3 mb-5">
  <div>
    {!! $breadcrumb !!}
  </div>
  <div class="mb-4 text-yellow d-flex justify-content-start align-items-center">
    <div id="latest-post-header-icon"></div>
    <h1>{{ $category->term->name }}</h1>
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
      @if($top_category->term->slug == 'magic-the-gathering')
        @include('guides.widgets.mtgTopDecks')
      @endif
      @include('guides.widgets.news')
    </div>
  </div>
  <div class="pagination-container text-center mt-2">
    @if( !$posts->onFirstPage() )
      <a href="{{ $posts->previousPageUrl() }}" class="mr-2"><i class="fas fa-angle-double-left"></i> Prev Page</a>
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