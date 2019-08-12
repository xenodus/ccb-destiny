<div class="col-md-12">
  <a href="{{ route('guide_post', ['slug' => $post->slug, 'id' => $post->ID]) }}" title="{{$post->title}}">
    <div class="post mb-4 d-flex flex-md-row flex-column align-items-stretch bg-white">
      <div class="post-overlay {{ $post->getColorCodedPostBorderCSS() }}"></div>
      <div class="text-center post-category {{ $post->getColorCodedPostBorderCSS(true) }}">{{ $post->getMainCategory() ? $post->getMainCategory()->term->name : '' }}</div>
      <div class="post-tn post-tn-horizontal lazy" style="background: url('{{ $post->getThumbnail('medium') ?? 'https://ccboys.xyz/images/og-banner-ccb.jpg' }}') no-repeat center 20%/cover;" data-bg="url({{ $post->getThumbnail('large') ?? 'https://ccboys.xyz/images/og-banner-ccb.jpg' }})">
      </div>
      <div class="post-info p-4 text-left">
        <div class="post-title mb-2 mt-1">{{ $post->post_title }}</div>
        <div class="post-excerpt mb-3" style="flex-shrink: 1;">{{ $post->getExcerpt() }}</div>
        <div class="post-author d-flex justify-content-start align-items-center mt-3">
          <div class="d-flex align-items-center">
            <img src="{{ $post->getAuthorAvatar() }}" class="post-author-avatar mr-1 " style="width: 20px; border-radius: 25px;">
            <div style="font-size: 0.7rem;">{{ $post->author->nickname }}</div>
          </div>
          <div class="d-flex align-items-center ml-4">
            <i class="far fa-calendar-alt mr-1" style="font-size: 0.9em;"></i>
            <div style="font-size: 0.7rem;">{{ $post->getPostDate() }}</div>
          </div>
        </div>
      </div>
    </div>
  </a>
</div>