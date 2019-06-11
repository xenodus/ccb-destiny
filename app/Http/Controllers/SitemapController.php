<?php
namespace App\Http\Controllers;

use Spatie\Sitemap\SitemapGenerator;
use Carbon\Carbon;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Classes\Post;
use App\Classes\Post_Taxonomy;

class SitemapController extends Controller
{
    public function crawl()
    {
        SitemapGenerator::create('https://ccboys.xyz')->writeToFile( public_path().'/sitemap.xml' );

        return response()->json(['status' => 1]);
    }

    public function generate()
    {
      $sitemap = Sitemap::create()
                  ->add(Url::create( str_replace('http://', 'https://', route('home')) ))
                  ->add(Url::create( str_replace('http://', 'https://', route('stats_raid')) ))
                  ->add(Url::create( str_replace('http://', 'https://', route('stats_weapons')) ))
                  ->add(Url::create( str_replace('http://', 'https://', route('stats_pve')) ))
                  ->add(Url::create( str_replace('http://', 'https://', route('stats_pvp')) ))
                  ->add(Url::create( str_replace('http://', 'https://', route('stats_gambit')) ))
                  ->add(Url::create( str_replace('http://', 'https://', route('guide_index')) ))
                  ->add(Url::create( str_replace('http://', 'https://', route('outbreak_solution')) ));

      // Posts
      $posts = Post::published()->get();

      foreach($posts as $post) {
        $sitemap->add(
          Url::create( str_replace('http://', 'https://', route('guide_post', ['slug' => $post->slug, 'id' => $post->ID])) )
          ->setLastModificationDate(Carbon::parse($post->post_modified))
        );
      }

      // Categories
      $categories = Post_Taxonomy::where('taxonomy', 'category')->get();

      foreach($categories as $cat) {
        $category_post_count = Post::published()->taxonomy('category', $cat->term->slug)->count();

        if( $category_post_count ) {
          $sitemap->add(
            Url::create( str_replace('http://', 'https://', route('guide_category', ['slug' => $cat->term->slug, 'id' => $cat->term_taxonomy_id])) )
            ->setLastModificationDate(Carbon::parse($post->post_modified))
          );
        }
      }

      $sitemap->writeToFile( public_path().'/sitemap.xml' );

      return response()->json(['status' => 1]);
    }
}