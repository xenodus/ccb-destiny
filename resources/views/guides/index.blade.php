@extends('layouts.template')

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/post.css') }}"/>
@endsection

@section('body')
<section id="guide-listing" class="text-center container-fluid mt-5 mb-5">
  <div class="container">
    <div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent pl-0" vocab="https://schema.org/" typeof="BreadcrumbList">
        <li class="breadcrumb-item" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage"
              href="{{ route('guide_index') }}">
            <span property="name">Guides</span></a>
          <meta property="position" content="1">
        </li>
      </ol>
    </div>
    <div class="row mb-5 categories-img">
      <div class="col-md-6 text-center">
        <a href="{{ route('guide_category', ['slug' => $destiny_category->term->slug, 'id' => $destiny_category->term_taxonomy_id]) }}">
          <div class="d-flex flex-column align-items-center justify-content-center item item-destiny mb-3">
            <div class="item-overlay animated"></div>
            <div class="item-name">Destiny 2</div>
          </div>
        </a>
      </div>
      <div class="col-md-6 text-center">
        <a href="{{ route('guide_category', ['slug' => $magic_category->term->slug, 'id' => $magic_category->term_taxonomy_id]) }}">
          <div class="d-flex align-items-center justify-content-center item item-mtg mb-3">
            <div class="item-overlay"></div>
            <div class="item-name">Magic: The Gathering Arena</div>
          </div>
        </a>
      </div>
    </div>
    <div class="mb-4 text-yellow d-flex justify-content-start align-items-center">
      <div id="latest-post-header-icon"></div>
      <h1>Latest Posts</h1>
    </div>
    <div class="row grid">
      <div class="grid-sizer"></div>
      <div class="gutter-sizer"></div>
      @foreach($posts as $post)
      <div class="col-md-4 grid-item">
        <div class="post mb-4 animated slow infinite {{ $post->isFeatured() ? 'featured-post' : '' }}">
          <a href="{{ route('guide_post', ['slug' => $post->slug, 'id' => $post->ID]) }}">
            <div class="post-tn {{ $post->getColorCodedPostBorderCSS() }}" style="background: url('{{ $post->thumbnail ?? 'https://ccboys.xyz/images/og-banner-ccb.jpg' }}') no-repeat center 20%/cover;">
              <div class="post-overlay"></div>
            </div>
            <div class="post-info p-3 text-left">
              <div class="post-item-date text-uppercase">
                <div>{{ \Carbon\Carbon::parse($post->getPostDate())->format('M') }}</div>
                <div>{{ \Carbon\Carbon::parse($post->getPostDate())->format('d') }}</div>
              </div>
              <div class="post-title mb-2">{{ $post->post_title }}</div>
              <div class="post-excerpt">{{ $post->getExcerpt() }}</div>
            </div>
          </a>
        </div>
      </div>
      @endforeach
    </div>
    <div class="pagination-container text-center">
      {{ $posts->links() }}
    </div>
  </div>
</section>
@endsection

@section('footer')
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script src="/js/post_listing.js"></script>
@endsection