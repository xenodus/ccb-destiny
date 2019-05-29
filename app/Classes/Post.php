<?php

namespace App\Classes;

use Corcel\Model\Post as Corcel;
use Corcel\Model\Taxonomy as WP_Post_Taxonomy;
use Carbon\Carbon;

class Post extends Corcel
{
  //protected $connection = 'wordpress';

  public function __construct(array $attributes = array())
  {
      parent::__construct($attributes);
  }

  public function isFeatured() {
    if ( $this->getTags()->filter(function($value, $key){ return $value->term->slug == 'featured'; })->count() )
      return true;
    else
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
    return Carbon::parse($this->post_date)->format('d M Y');
  }

  function getJSONLD() {

    $post = $this::with('attachment')
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

  function customParseContent() {
    $this->replaceOriginalImages();
    $this->convertBootstrapEmbed();
    $this->replaceYoutubeEmbed();
    $this->applyLightbox();
    $this->applyMTG();
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

  function getColorCodedPostBorderCSS() {

    if( $this->getCategories()->filter(function($value, $key){ return $value->term->slug == 'destiny'; })->count() ) {
      return 'post-destiny';
    }
    else if( $this->getCategories()->filter(function($value, $key){ return $value->term->slug == 'magic-the-gathering'; })->count() ) {
      return 'post-magic';
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

  public static function makeBreadCrumbsFromCategory($category) {
    $parent_category = WP_Post_Taxonomy::find($category->parent);
    $children_categories = WP_Post_Taxonomy::where('parent', $category->term_taxonomy_id)->get();
    $category_post_count = Post::published()->taxonomy('category', $category->term->slug)->count();
    $i = 1;

    // Opening
    $html = '
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent pl-0" vocab="https://schema.org/" typeof="BreadcrumbList">
        <li class="breadcrumb-item" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage"
              href="'.route('guide_index').'">
            <span property="name">Guides</span></a>
          <meta property="position" content="'.$i.'">
        </li>';

    // Parents
    $parent_categories = [];
    if( $parent_category ) {
      while( $parent_category ) {
        $parent_categories[] = $parent_category;
        $parent_category = WP_Post_Taxonomy::find($parent_category->parent);
      }
    }

    if($parent_categories) {
      $parent_categories = array_reverse($parent_categories);

      foreach($parent_categories as $cat) {
        $category_post_count = Post::published()->taxonomy('category', $cat->term->slug)->count();

        if( $category_post_count ) {
          $html .= '
            <li class="breadcrumb-item children-item" property="itemListElement" typeof="ListItem">
              <a property="item" typeof="WebPage"
                  href="'.route('guide_category', ['slug' => $cat->term->slug, 'id' => $cat->term_taxonomy_id]).'">
                <span property="name">'.$cat->term->name.' ('.$category_post_count.')</span></a>
              <meta property="position" content="'.++$i.'">
            </li>';
        }
      }
    }

    // Self
    $html .= '
        <li class="breadcrumb-item active" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage"
              href="'.route('guide_category', ['slug' => $category->term->slug, 'id' => $category->term_taxonomy_id]).'">
            <span property="name">'.$category->term->name.' ('.$category_post_count.')</span></a>
          <meta property="position" content="'.++$i.'">
        </li>';

    // Children

    foreach($children_categories as $cat) {

      $category_post_count = Post::published()->taxonomy('category', $cat->term->slug)->count();

      if( $category_post_count ) {
        $html .= '
          <li class="breadcrumb-item children-item" property="itemListElement" typeof="ListItem">
            <a property="item" typeof="WebPage"
                href="'.route('guide_category', ['slug' => $cat->term->slug, 'id' => $cat->term_taxonomy_id]).'">
              <span property="name">'.$cat->term->name.' ('.$category_post_count.')</span></a>
            <meta property="position" content="'.++$i.'">
          </li>';
      }
    }

    // Closing
    $html .= '
      </ol>
    </nav>';

    return $html;
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
            <span property="name">'.$cat->term->name.' ('.$category_post_count.')</span></a>
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
      $categories[] = $taxonomy;
    }

    return collect($categories);
  }

  function getTags() {
    $post = $this::with('taxonomies')
          ->find($this->ID);

    $tags = [];

    $post->taxonomies = $post->taxonomies->where('taxonomy', 'post_tag')->all();

    foreach($post->taxonomies as $taxonomy) {
      $tags[] = $taxonomy;
    }

    return collect($tags);
  }
}