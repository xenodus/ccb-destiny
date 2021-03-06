<?php
namespace App\Http\Controllers;

use Corcel;
use App\Classes\Post as WP_Post;
use App\Classes\Post_Taxonomy as WP_Post_Taxonomy;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function category($slug='', $id)
    {
        // featured
        $featured_posts = WP_Post::published()
            ->with('taxonomies')
            ->taxonomy('post_tag', 'featured')
            ->whereHas('taxonomies', function($q) use ($id) {
                $q->where('taxonomy', 'category')->whereHas('term', function($q) use ($id) {
                    $q->where('term_id', $id);
                });
            })
            ->newest()
            ->get();

        // non featured
        $non_featured_posts = WP_Post::published()
            ->whereDoesntHave('taxonomies', function($q) {
                $q->where('taxonomy', 'post_tag')
                    ->whereHas('term', function($q) {
                        $q->where('slug', 'featured');
                    });
            })
            ->with('taxonomies')
            ->whereHas('taxonomies', function($q) use ($id) {
                $q->where('taxonomy', 'category')->whereHas('term', function($q) use ($id) {
                    $q->where('term_id', $id);
                });
            })
            ->newest()
            ->get();

        $posts = $featured_posts->merge($non_featured_posts)->where('post_type', 'post');

        if( !$posts->count() ) return redirect()->route('guide_index'); // category don't exists

        $data['category'] = WP_Post_Taxonomy::find($id);

        if( $data['category']->term->slug != $slug )
            return redirect()->route('guide_category', ['slug' => $data['category']->term->slug,'id' => $id]); // wrong slug

        $data['top_category'] = $data['category']->getTopLevelCategory();

        // featured first
        $data['posts'] = $posts->paginate( 10 );
        $data['site_title'] = $data['category']->term->name . ' | Guides | ' . env('SITE_NAME');
        $data['active_page'] = 'guides';
        $data['breadcrumb'] = $data['category']->makeBreadcrumbsFromCategory();

        return view('guides.category', $data);
    }

    public function get_latest()
    {
        // featured
        $featured_posts = WP_Post::published()
            ->whereDoesntHave('taxonomies', function($q) {
                $q->where('taxonomy', 'category')->whereHas('term', function($q) {
                    $q->where('slug', 'clan');
                });
            })
            ->taxonomy('post_tag', 'featured')
            //->orderBy('post_modified', 'desc')
            ->newest()
            ->get();

        $non_featured_posts = WP_Post::published()
            ->whereDoesntHave('taxonomies', function($q) {
                $q->where('taxonomy', 'category')->whereHas('term', function($q) {
                    $q->where('slug', 'clan');
                });
            })
            ->whereDoesntHave('taxonomies', function($q) {
                $q->where('taxonomy', 'post_tag')->whereHas('term', function($q) {
                    $q->where('slug', 'featured');
                });
            })
            //->orderBy('post_modified', 'desc')
            ->newest()
            ->get();

        $posts = $featured_posts->merge($non_featured_posts)->where('post_type', 'post')->take(3);;

        $payload = [];

        foreach($posts as $post) {

            if( $post->getCategories()->filter(function($value, $key){ return $value->term->slug == 'destiny'; })->count() )
                $color_code = $post::DESTINY_COLOR_CODE;
            else if( $post->getCategories()->filter(function($value, $key){ return $value->term->slug == 'magic-the-gathering'; })->count() )
                $color_code = $post::MTG_COLOR_CODE;
            else if( $post->getCategories()->filter(function($value, $key){ return $value->term->slug == 'the-division-2'; })->count() )
                $color_code = $post::DIVISION_COLOR_CODE;
            else
                $color_code = '#ffc107';

            // Large TN
            if( isset( $post->getThumbnail('large')->attachment->url ) )
                $thumbnail_large = $post->getThumbnail('large')->attachment->url;
            elseif( $post->getThumbnail('large') )
                $thumbnail_large = $post->getThumbnail('large');
            else
                $thumbnail_large = '';

            $payload[] = [
                'id' => $post->ID,
                'title' => $post->post_title,
                'image' => $post->image,
                'thumbnail' => $post->getThumbnail('medium') ?? '',
                'thumbnail_large' => $thumbnail_large,
                'url' => route('guide_post', ['slug' => $post->slug, 'id' => $post->ID]),
                'excerpt' => $post->getExcerpt(),
                'color_code' => $color_code,
                'category' => $post->getMainCategory()->term->name
            ];
        }

        return response()->json($payload);
    }

    public function index()
    {
        $data['destiny_category'] = WP_Post_Taxonomy::find(3);
        $data['magic_category'] = WP_Post_Taxonomy::find(4);
        $data['division_category'] = WP_Post_Taxonomy::find(33);

        // featured
        $featured_posts = WP_Post::published()
            ->whereDoesntHave('taxonomies', function($q) {
                $q->where('taxonomy', 'category')->whereHas('term', function($q) {
                    $q->where('slug', 'clan');
                });
            })
            ->taxonomy('post_tag', 'featured')
            ->newest()
            ->get();

        // non featured
        $non_featured_posts = WP_Post::published()
            ->whereDoesntHave('taxonomies', function($q) {
                $q->where('taxonomy', 'category')->whereHas('term', function($q) {
                    $q->where('slug', 'clan');
                });
            })
            ->whereDoesntHave('taxonomies', function($q) {
                $q->where('taxonomy', 'post_tag')->whereHas('term', function($q) {
                    $q->where('slug', 'featured');
                });
            })
            ->newest()
            ->get();

        // featured first
        $data['posts'] = $featured_posts->merge($non_featured_posts)->where('post_type', 'post')->paginate( 10 );
        $data['site_title'] = 'Guides | ' . env('SITE_NAME');
        $data['active_page'] = 'guides';

        return view('guides.index', $data);
    }

    public function post($slug='', $id, Request $request)
    {
        if( $request->query('preview') ) {
            $post = WP_Post::with('taxonomies')
                ->with('attachment')
                ->where('post_type', 'post')
                ->find($id);
        }
        else {
            $post = WP_Post::published()
                ->with('taxonomies')
                ->with('attachment')
                ->where('post_type', 'post')
                ->find($id);
        }

        if( !$post || ($post->post_status != 'publish' && !$request->query('preview')) ) abort(404);

        if( !$request->query('preview') )
            if( $post->slug != $slug  ) return redirect()->route('guide_post', ['slug' => $post->slug,'id' => $id]);

        $data['top_category'] = $post->getMainCategory();

        $data['post'] = $post;
        $data['active_page'] = 'guides';

        // og tags
        $data['site_title'] = $post->post_title . ' | ' . env('SITE_NAME');
        $data['site_image'] = $post->thumbnail ?? '';
        $data['site_url'] = route('guide_post', ['slug' => $post->slug,'id' => $id]);
        $data['site_description'] = $post->getExcerpt();

        return view('guides.post', $data);
    }
}