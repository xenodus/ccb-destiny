<?php

namespace App\Classes;

use Corcel\Model\Taxonomy as WP_Post_Taxonomy;
use Carbon\Carbon;

class Post_Taxonomy extends WP_Post_Taxonomy
{

  function getTopLevelCategory() {

    if( $this->parent == 0 )
      return $this;
    else {
      $parent_category = Post_Taxonomy::find($this->parent);

      $top_category = null;

      if( $parent_category ) {
        while( $parent_category ) {
          $top_category = $parent_category;
          $parent_category = Post_Taxonomy::find($parent_category->parent);
        }
      }

      return $top_category;
    }
  }

  function makeBreadcrumbsFromCategory() {
    $parent_category = WP_Post_Taxonomy::find($this->parent);
    $children_categories = WP_Post_Taxonomy::where('parent', $this->term_taxonomy_id)->get();
    $category_post_count = Post::published()->taxonomy('category', $this->term->slug)->count();
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
                <span property="name">'.$cat->term->name.'</span></a>
              <meta property="position" content="'.++$i.'">
            </li>';
        }
      }
    }

    $category_post_count = Post::published()->taxonomy('category', $this->term->slug)->count();

    // Self
    $html .= '
        <li class="breadcrumb-item active" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage"
              href="'.route('guide_category', ['slug' => $this->term->slug, 'id' => $this->term_taxonomy_id]).'">
            <span property="name">'.$this->term->name.' ('.$category_post_count.')</span></a>
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
              <span property="name">'.$cat->term->name.'</span></a>
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
}