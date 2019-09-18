<?php

namespace App\Classes;

use Corcel\Model\Post as WP_Post;
use App\Classes\Post_Taxonomy as WP_Post_Taxonomy;
use Carbon\Carbon;

class Post extends WP_Post
{
  const RELATED_POST_COUNT = 6;

  const DESTINY_COLOR_CODE = '#b78c25';
  const MTG_COLOR_CODE = '#0F9D58';
  const DIVISION_COLOR_CODE = '#4285F4';

  // Mutator
  public function getContentAttribute()
  {
    $this->replaceOriginalImages();
    $this->convertBootstrapEmbed();
    $this->replaceYoutubeEmbed();
    $this->applyLightbox();
    $this->applyMTG();

    return $this->stripShortcodes($this->post_content);
  }

  public function getThumbnail($size='original')
  {
    if( $size == 'original' || $size == '' ) {
      return $this->thumbnail;
    }
    else {
      if( isset($this->thumbnail) && isset( $this->thumbnail->size($size)['url'] ) ) {
        return $this->thumbnail->size($size)['url'];
      }
      else {
        return $this->thumbnail;
      }
    }
  }

  // Related posts - same child category first
  public function related() {
    $post_categories = $this->getCategories();

    if( $post_categories->count() > 0 ) {

      $posts = self::published()
        ->taxonomy('category', $post_categories->last()->term->slug)
        ->where('id', '!=', $this->ID)
        ->take( self::RELATED_POST_COUNT )
        ->get();

      if( $post_categories->count() > 1 && $post_categories->count() < self::RELATED_POST_COUNT ) {
        $parent_category_posts = self::published()
          ->taxonomy('category', $post_categories->first()->term->slug)
          ->where('id', '!=', $this->ID)
          ->whereNotIn('id', $posts->map(function($value, $key){ return $value->ID; })->toArray())
          ->take( self::RELATED_POST_COUNT - $posts->map(function($value, $key){ return $value->ID; })->count() )
          ->get();

        $posts = $posts->merge($parent_category_posts);
      }

      return $posts;
    }

    return null;
  }

  // Next post from the same category
  public function next() {
    $category = $this->getMainCategory();
    $next_post = self::published()->taxonomy('category', $category->term->slug)->where('id', '>', $this->ID)->first();
    return $next_post;
  }

  // Prev post from the same category
  public function prev() {
    $category = $this->getMainCategory();
    $prev_post = self::published()->taxonomy('category', $category->term->slug)->where('id', '<', $this->ID)->orderBy('id', 'desc')->first();
    return $prev_post;
  }

  public function isFeatured() {
    if ( $this->getTags()->filter(function($value, $key){ return $value->term->slug == 'featured'; })->count() )
      return true;

    return false;
  }

  public function getAuthorAvatar() {
    if( $this->author->meta->wp_user_avatar ) {
        $avatar = $this::find( $this->author->meta->wp_user_avatar )->guid;
        $avatar = str_replace('.jpg', '-150x150.jpg', $avatar);

        return $avatar;
    }

    return null;
  }

  public function getPostDate() {
    return Carbon::parse($this->post_date)->format('j M Y');
  }

  public function getModifiedDate() {
    return Carbon::parse($this->post_modified)->format('j M Y');
  }

  function getJSONLD() {

    $post = self::with('attachment')
          ->find($this->ID);

    $images = '';

    if( $post->attachment->count() ) {
      $images = $post->attachment->map(function($item, $key){
        return '"'.$item->guid.'"';
      });

      $images = implode(', ', $images->toArray());
    }

    $datePublished = Carbon::createFromFormat('Y-m-d H:i:s', $this->post_date)->format(\DateTime::ISO8601);
    $dateModified = Carbon::createFromFormat('Y-m-d H:i:s', $this->post_modified)->format(\DateTime::ISO8601);

    return '
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "NewsArticle",
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "https://google.com/article"
      },
      "headline": "'.$this->post_title.'",
      "image": ['.$images.'],
      "datePublished": "'.$datePublished.'",
      "dateModified": "'.$dateModified.'",
      "author": {
        "@type": "Person",
        "name": "'.$this->author->nickname.'"
      },
       "publisher": {
        "@type": "Organization",
        "name": "CCBoys",
        "logo": {
          "@type": "ImageObject",
          "url": "https://ccboys.xyz/images/og-banner-ccb.jpg"
        }
      },
      "description": "'.$this->getExcerpt().'"
    }
    </script>';
  }

  function applyMTG() {

    preg_match_all("/\[\[(.*?)\]\]/", $this->post_content, $matches);

    if( count($matches) ) {
      foreach($matches[0] as $key => $src) {

        preg_match_all("/<(.*)>(.*)<\/(.*)>/", $src, $style_matches);

        if( count($style_matches[0]) )
          $new_src = '<'.$style_matches[1][0].'><span class="mtgtooltip">'.$style_matches[2][0].'</span></'.$style_matches[3][0].'/>';
        else
          $new_src = '<span class="mtgtooltip">'.$matches[1][$key].'</span>';

        $this->post_content = str_replace($src, $new_src, $this->post_content);
      }
    }
  }

  function replaceOriginalImages() {
    $post = $this::with('attachment')
          ->find($this->ID);

    preg_match_all('/class="wp-image-(\d*)"/', $post->post_content, $matches);

    if( count($matches) >= 2 ) {
      foreach( $matches[1] as $image_id ) {
          $img_src_matches = [];
          preg_match_all('/<img src="(.*)" alt=".*" class="wp-image-'.$image_id.'"\/>/', $post->post_content, $img_src_matches);

          if( isset( $img_src_matches[1][0] ) ) {
              $img_url = $img_src_matches[1][0];

              if( $post->attachment->where('ID', $image_id)->first() ) {
                  $original_img_url = $post->attachment->where('ID', $image_id)->first()->guid;
                  $post->post_content = str_replace($img_url, $original_img_url, $post->post_content);
              }
          }
      }
    }

    $this->post_content = $post->post_content;
  }

  function replaceYoutubeEmbed() {

    preg_match_all('/<figure class="wp-block-embed-youtube.*"><div .*>(.*)<\/div><\/figure>/s', $this->post_content, $matches);

    if( count($matches) ) {
        foreach($matches[0] as $key => $vid_src) {
            $vid_link = str_replace('watch?v=', 'embed/', trim($matches[1][$key]));
            preg_match('/http.*embed\/(.*)&.*/', $vid_link, $vid_link_matches);

            if( count($vid_link_matches) ) {
              $vid_link = 'https://www.youtube.com/embed/' . $vid_link_matches[0];
            }

            $new_iframe = '
            <div class="embed-responsive embed-responsive-16by9">
              <iframe class="embed-responsive-item" src="'.str_replace('watch?v=', 'embed/', trim($matches[1][$key])).'"></iframe>
            </div>
            ';
            $this->post_content = str_replace($vid_src, $new_iframe, $this->post_content);
        }
    }
  }

  function applyLightbox() {
    preg_match_all('/<img src="(.*)" alt=".*" class="wp-image-(\d*)".*\/>/', $this->post_content, $matches);

    if( count($matches) ) {
        foreach($matches[0] as $key => $img_src) {
            $new_img_src = '<a href="'.$matches[1][$key].'" data-lightbox="'.$matches[2][$key].'">'.$img_src.'</a>';
            $this->post_content = str_replace($img_src, $new_img_src, $this->post_content);
        }
    }
  }

  function convertBootstrapEmbed() {
    $post_content = $this->post_content;
    $post_content = str_replace('<iframe', '<iframe class="embed-responsive-item"', $post_content);
    $post_content = str_replace('<iframe', '<div class="embed-responsive embed-responsive-16by9"><iframe', $post_content);
    $post_content = str_replace('</iframe>', '</iframe></div>', $post_content);

    $this->post_content = $post_content;
  }

  function getColorCodedPostBorderCSS($horizontal=false) {

    if( $this->getCategories()->filter(function($value, $key){ return $value->term->slug == 'destiny'; })->count() ) {
      return $horizontal ? 'post-destiny-horizontal' : 'post-destiny';
    }
    else if( $this->getCategories()->filter(function($value, $key){ return $value->term->slug == 'magic-the-gathering'; })->count() ) {
      return $horizontal ? 'post-magic-horizontal' : 'post-magic';
    }
    else if( $this->getCategories()->filter(function($value, $key){ return $value->term->slug == 'the-division-2'; })->count() ) {
      return $horizontal ? 'post-division-horizontal' : 'post-division';
    }
    return '';
  }

  function getExcerpt( $length = 20, $more = '...' ) {

    if( $this->excerpt ) {
      return $this->excerpt;
    }
    else {
      $content = $this->post_content;
      $excerpt = strip_tags( trim( $content ) );
      $words = str_word_count( $excerpt, 2 );
      if ( count( $words ) > $length ) {
        $words = array_slice( $words, 0, $length, true );
        end( $words );
        $position = key( $words ) + strlen( current( $words ) );
        $excerpt = substr( $excerpt, 0, $position ) . $more;
      }

      $excerpt = trim(preg_replace('/\s+/', ' ', $excerpt));

      return $excerpt;
    }
  }

  function getMainCategory() {
    $cat = $this->getCategories()->first();

    if( $cat )
      return $cat->getTopLevelCategory();
    else
      return null;
  }

  function makeBreadcrumbs() {

    // rich data reference: https://developers.google.com/search/docs/data-types/breadcrumb

    $html = '
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent pl-0" vocab="https://schema.org/" typeof="BreadcrumbList">
        <li class="breadcrumb-item" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage"
              href="'.route('guide_index').'">
            <span property="name">Guides</span></a>
          <meta property="position" content="1">
        </li>';

    $i = 1;
    $categories = $this->getCategoryBreadcrumbs();

    foreach($categories as $cat) {

      $category_post_count = Post::published()->taxonomy('category', $cat->term->slug)->count();

      $html .= '
        <li class="breadcrumb-item" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage"
              href="'.route('guide_category', ['slug' => $cat->term->slug, 'id' => $cat->term_taxonomy_id]).'">
            <span property="name">'.$cat->term->name.'</span></a>
          <meta property="position" content="'.++$i.'">
        </li>';
    }

    $html .= '
        <li class="breadcrumb-item active" aria-current="page" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage"
              href="'.route('guide_post', ['slug' => $this->slug, 'id' => $this->ID]).'">
            <span property="name">'.$this->post_title.'</span></a>
          <meta property="position" content="'.++$i.'">
        </li>
      </ol>
    </nav>';

    return $html;
  }

  function getCategoryBreadcrumbs() {
    $breadcrumbs = [];
    $parentID = 0;
    $categories = $this->getCategories();

    // Get top level category first
    if( $categories->where('parent', 0)->first() ) {
      $breadcrumbs[] = $categories->where('parent', 0)->first();
      $parentID = $categories->where('parent', 0)->first()->term_taxonomy_id;
    }

    // Get child categories
    while( $parentID != 0 ) {
      if( $categories->where('parent', $parentID)->first() ) {
        $breadcrumbs[] = $categories->where('parent', $parentID)->first();
        $parentID = $categories->where('parent', $parentID)->first()->term_taxonomy_id;
      }
      else {
        $parentID = 0;
      }
    }

    return $breadcrumbs;
  }

  function getCategories() {
    $post = $this::with('taxonomies')
          ->find($this->ID);

    $categories = [];

    $post->taxonomies = $post->taxonomies->where('taxonomy', 'category')->all();

    foreach($post->taxonomies as $taxonomy) {
      $categories[] = WP_Post_Taxonomy::find($taxonomy->term_taxonomy_id);
    }

    return collect($categories);
  }

  function getTags() {
    $post = $this::with('taxonomies')
          ->find($this->ID);

    $tags = [];

    $post->taxonomies = $post->taxonomies->where('taxonomy', 'post_tag')->all();

    foreach($post->taxonomies as $taxonomy) {
      $tags[] = WP_Post_Taxonomy::find($taxonomy->term_taxonomy_id);
    }

    return collect($tags);
  }
}