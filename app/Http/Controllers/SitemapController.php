<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $content = Cache::remember('sitemap_index', 3600, function () {
            return view('sitemaps.index')->render();
        });
        return response($content, 200, ['Content-Type' => 'application/xml']);
    }

    public function posts(): Response
    {
        $posts = Post::published()
            ->select('slug', 'updated_at', 'published_at')
            ->orderByDesc('updated_at')
            ->get();

        $content = view('sitemaps.posts', compact('posts'))->render();
        return response($content, 200, ['Content-Type' => 'application/xml']);
    }

    public function categories(): Response
    {
        $categories = Category::select('slug', 'updated_at')->get();
        $content = view('sitemaps.categories', compact('categories'))->render();
        return response($content, 200, ['Content-Type' => 'application/xml']);
    }

    public function pages(): Response
    {
        $content = view('sitemaps.pages')->render();
        return response($content, 200, ['Content-Type' => 'application/xml']);
    }
}
