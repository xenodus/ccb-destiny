<?php
namespace App\Http\Controllers;

use Spatie\Sitemap\SitemapGenerator;

class SitemapController extends Controller
{
    public function crawl()
    {
        SitemapGenerator::create('https://ccboys.xyz')->writeToFile( public_path().'/sitemap.xml' );

        return response()->json(['status' => 1]);
    }
}