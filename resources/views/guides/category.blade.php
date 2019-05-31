@extends('layouts.template')

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/post.css') }}"/>
@endsection

@section('body')
<section id="guide-listing" class="text-center container-fluid mt-3 mb-5">
  <div class="container">
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
        @if($top_category->term->slug == 'magic-the-gathering')
          @include('guides.widgets.mtgTopDecks')
        @endif

        @include('guides.widgets.news')
      </div>
    </div>
    <div class="pagination-container text-center mt-2">
      @if( !$posts->onFirstPage() )
        <a href="{{ $posts->previousPageUrl()  }}"> <i class="fas fa-angle-double-left"></i> Prev Page</a>
      @endif
      @if( $posts->hasMorePages()  )
        <a href="{{ $posts->nextPageUrl()  }}">Next Page <i class="fas fa-angle-double-right"></i></a>
      @endif
    </div>
  </div>
</section>
@endsection

@section('footer')
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script src="/js/post_listing.js"></script>
@if($top_category->term->slug == 'magic-the-gathering')
<script src="/js/mtg_top_decks.js?<?=time()?>"></script>
@endif
<script type="text/javascript">
$(document).ready(function(){
  var category_map = {
    'magic-the-gathering': 'magic',
    'the-division-2': 'division',
    'destiny': 'destiny',
  };

  $.get('/api/news/get/' + category_map['{{$top_category->term->slug}}'], function(res){

    $("#news-listing-{{$top_category->term->slug}} .loader").hide();

    if( res.length ) {
      for(var i=0; i<res.length; i++) {
        $("#news-listing-{{$top_category->term->slug}}").append("<div class='mb-2'>"+(i+1)+". <a href='"+res[i].url+"' target='_blank'>"+res[i].title+"</a> | "+res[i].source+"</div>");
      }
    }
    else {
      $("#news-widget-{{$top_category->term->slug}}").remove();
    }
  });
})
</script>
@endsection