@extends('layouts.template')

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/post.css') }}"/>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
(adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: "ca-pub-2393161407259792",
    enable_page_level_ads: true
});
</script>
@endsection

@section('body')
<section id="guide-post" class="text-center container mt-3 mb-4">
  <div id="guide-post-breadcrumbs">
  {!! $post->makeBreadcrumbs() !!}
  </div>
  <div class="row">
    <div class="col-md-12">
      <div id="post-meta" class="border-secondary border-bottom d-flex flex-column justify-content-end text-left mb-4" style="<?=($post->thumbnail?'min-height: 450px; background-image: linear-gradient(to top, rgba(0,0,0,0.5), rgba(0,0,0,0)), url('.$post->thumbnail.');':'background: #000;')?>">
        <div style="background: linear-gradient(to top, rgba(0,0,0,1), rgba(0,0,0,0.5));" class="pt-md-4 pb-md-4 p-3">
          <div>
            <h1 class="mb-0">{{ $post->post_title }}</h1>
          </div>
          <div class="ml-1 mt-1">
            <span class="author-name mr-md-4 mr-3">
            @if( $post->getAuthorAvatar() )
            <img src="{{ $post->getAuthorAvatar() }}" class="author-avatar mr-1">
            @else
            <i class="fas fa-user mr-1"></i>
            @endif
            {{ $post->author->nickname }}
            </span>
            <span class="post-date d-inline-block mr-md-4 mr-3"><i class="fas fa-calendar-alt mr-1"></i>Published @ {{ $post->getPostDate() }}</span>
            @if( $post->getPostDate() != $post->getModifiedDate() )
            <span class="post-date d-inline-block"><i class="far fa-calendar-plus mr-1"></i> Last Updated @ {{ $post->getModifiedDate() }}</span>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-9">
      <div id="guide-post-content" class="mb-5 pl-md-2 pr-md-2">
        {!! $post->content !!}
      </div>

      @include('ads.horizontal')

      @if( $post->related()->count() )
      <div id="related-posts-container" class="pt-3 mt-md-3 mt-4">
        <div class="row no-gutters">
          <div class="col-md-12">
            <div class="related-posts-header text-yellow d-flex justify-content-start align-items-center mb-3" style="border-bottom: 2px solid #ffca00;">
              <div id="related-post-header-icon" class="mr-1"></div>
              <h2 class="mb-1 text-yellow">Related Guides</h2>
            </div>
            <div class="row">
              @foreach($post->related() as $p)
              <div class="col-md-4 {{ $loop->iteration > 3 ? 'mobile-hide' : '' }}">
                <a href="{{ route('guide_post', ['slug' => $p->slug, 'id' => $p->ID]) }}" title="{{$p->title}}">
                  <div class="related-posts-item mb-3">
                    <div class="related-posts-tn d-flex flex-column justify-content-center" style="background: url('{{ $p->thumbnail ?? 'https://ccboys.xyz/images/og-banner-ccb.jpg' }}') no-repeat center 20%/cover;">
                    </div>
                    <div class="related-posts-title bg-white text-dark {{ $post->getColorCodedPostBorderCSS() }}">
                      <div class="p-2">
                        <small>{{ $p->title }}</small>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
      @endif

    </div>
    <div class="col-md-3 col-sm-12">
      <div>
        @include('guides.widgets.sidebarNav')
        @if($top_category->term->slug == 'magic-the-gathering')
          @include('guides.widgets.mtgTopDecks')
        @endif
        @include('guides.widgets.news')
      </div>
    </div>
  </div>
</section>
@endsection

@section('footer')
<script type="text/javascript">
  var news_listing_category_slug = "{{$top_category->term->slug ?? ''}}";
</script>
<script src="{{ mix('/js/compiled/post.js') }}"></script>
{!! $post->getJSONLD() !!}
@endsection