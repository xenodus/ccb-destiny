<div class="<?=(( isset($category) && $category->term_taxonomy_id == 4 ) ? 'col-md-6' : 'col-md-4')?> grid-item">
  <div class="post mb-4">
    <a href="{{ route('guide_post', ['slug' => $post->slug, 'id' => $post->ID]) }}">
      <div class="post-tn {{ $post->getColorCodedPostBorderCSS() }}" style="background: url('{{ $post->thumbnail ?? 'https://ccb-destiny.com/images/og-banner-ccb.jpg' }}') no-repeat center 20%/cover;">
        <div class="post-overlay"></div>
      </div>
      <div class="post-info p-3 text-left">
        <!--div class="post-item-date text-uppercase">
          <div>{{ \Carbon\Carbon::parse($post->getPostDate())->format('M') }}</div>
          <div>{{ \Carbon\Carbon::parse($post->getPostDate())->format('d') }}</div>
        </div-->
        <div class="post-title mb-1">{{ $post->post_title }}</div>
        <div class="post-excerpt mb-2">{{ $post->getExcerpt() }}</div>
        <!--div class="post-author d-flex justify-content-between align-items-center mt-3">
          <div class="d-flex align-items-center">
            <i class="far fa-calendar-alt mr-1" style="font-size: 0.9em;"></i>
            <div style="font-size: 0.7rem; font-weight: bold;">{{ $post->getPostDate() }}</div>
          </div>
          <div class="d-flex align-items-center">
            <img src="{{ $post->getAuthorAvatar() }}" class="post-author-avatar mr-1 " style="width: 20px; border-radius: 25px;">
            <div style="font-size: 0.7rem; font-weight: bold;">{{ $post->author->nickname }}</div>
          </div>
        </div-->
      </div>
    </a>
  </div>
</div>