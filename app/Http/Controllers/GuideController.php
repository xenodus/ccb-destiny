<?php
namespace App\Http\Controllers;

use App\Classes\Post as WP_Post;
use Corcel\Model\Taxonomy as WP_Post_Taxonomy;
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

        $posts = $featured_posts->merge($non_featured_posts);

        if( !$posts->count() ) return redirect()->route('guide_index'); // category don't exists

        $data['children_categories'] = WP_Post_Taxonomy::where('parent', $id)->get();

        $data['category'] = WP_Post_Taxonomy::find($id);

        if( $data['category']->term->slug != $slug )
            return redirect()->route('guide_category', ['slug' => $data['category']->term->slug,'id' => $id]); // wrong slug

        // featured first
        $data['posts'] = $posts->paginate( 9 );
        $data['site_title'] = $data['category']->term->name . ' | Guides | ' . env('SITE_NAME');
        $data['active_page'] = 'guides';
        $data['breadcrumb'] = WP_Post::makeBreadCrumbsFromCategory($data['category']);

        return view('guides.category', $data);
    }

    public function index()
    {
        $data['destiny_category'] = WP_Post_Taxonomy::find(3);
        $data['magic_category'] = WP_Post_Taxonomy::find(4);

        $data['destiny_category_post_count'] = WP_Post::taxonomy('category', $data['destiny_category']->term->slug)->count();
        $data['magic_category_post_count'] = WP_Post::taxonomy('category', $data['magic_category']->term->slug)->count();

        // featured
        $featured_posts = WP_Post::published()
            ->taxonomy('post_tag', 'featured')
            ->newest()
            ->get();

        // non featured
        $non_featured_posts = WP_Post::published()
            ->whereDoesntHave('taxonomies', function($q) {
                $q->where('taxonomy', 'post_tag')->whereHas('term', function($q) {
                    $q->where('slug', 'featured');
                });
            })
            ->newest()
            ->get();

        // featured first
        $data['posts'] = $featured_posts->merge($non_featured_posts)->paginate( 9 );
        $data['site_title'] = 'Guides | ' . env('SITE_NAME');
        $data['active_page'] = 'guides';

        return view('guides.index', $data);
    }

    public function post($slug='', $id, Request $request)
    {
        if( $request->query('preview') ) {
            $post = WP_Post::with('taxonomies')
                ->with('attachment')
                ->find($id);
        }
        else {
            $post = WP_Post::published()
                ->with('taxonomies')
                ->with('attachment')
                ->find($id);
        }

        if( !$post || ($post->post_status != 'publish' && !$request->query('preview')) ) abort(404);

        if( !$request->query('preview') )
            if( $post->slug != $slug  ) return redirect()->route('guide_post', ['slug' => $post->slug,'id' => $id]);

        //dd( $post->taxonomies('post_tag')->first() );
        //dd($post->getExcerpt());
        //dd($post->post_status);
        //dd($post->thumbnail);
        //dd($post->post_title);
        //dd($post->post_excerpt);
        //dd($post->author->nickname);
        //dd($post->attachment);
        //dd($post->post_content);

        $post->customParseContent();

        $post->getJSONLD();

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