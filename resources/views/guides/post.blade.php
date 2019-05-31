@extends('layouts.template')

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/post.css') }}"/>
@endsection

@section('body')
<section id="guide-post" class="text-center container-fluid mt-3 mb-4">
  <div class="container">
    <div id="guide-post-breadcrumbs">
    {!! $post->makeBreadcrumbs() !!}
    </div>
    <div id="post-meta" class="text-left mt-2 mb-4">
      <h1>{{ $post->post_title }}</h1>
      <div class="ml-1 mt-1">
        <span class="author-name">
        @if( $post->getAuthorAvatar() )
        <img src="{{ $post->getAuthorAvatar() }}" class="author-avatar">
        @else
        <i class="fas fa-user mr-1"></i>
        @endif
        {{ $post->author->nickname }}
        </span>
        <span class="post-date"><i class="fas fa-calendar-alt ml-3 mr-1"></i> {{ $post->getPostDate() }}</span>
      </div>
    </div>
    <div id="guide-post-content">
      {!! $post->content !!}
    </div>
  </div>
</section>
@endsection

@section('footer')
<script src="{{ mix('/js/compiled/post.js') }}"></script>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
  tooltips = new mtgTooltip({
    'basiclands': 'zzz',
    'shocklands': 'new',
    'fetchlands': 'new',
    'painlands': 'new',
    'mobile': false,
    'lazyload': false
  });
});
</script>
{!! $post->getJSONLD() !!}
@endsection