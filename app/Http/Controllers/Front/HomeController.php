<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $trending = Post::published()
            ->with('category')
            ->withCount(['downloadClicks as weekly_downloads' => function ($q) {
                $q->whereBetween('clicked_at', [now()->startOfWeek(), now()->endOfWeek()]);
            }])
            ->orderByDesc('weekly_downloads')
            ->limit(10)
            ->get();

        $latestSoftware = Post::published()->with('category')->ofType('software')->latest('published_at')->limit(8)->get();

        $latestApks = Post::published()->with('category')->ofType('apk')->latest('published_at')->limit(6)->get();

        $latestArticles = Post::published()
            ->with('category')
            ->whereIn('type', ['blog', 'tutorial'])
            ->latest('published_at')
            ->limit(6)
            ->get();

        $categories = Category::whereNull('parent_id')->withCount('posts')->orderBy('sort_order')->limit(12)->get();

        $sidebarTrending = Cache::remember('sidebar_trending', 3600, function () {
            return Post::published()->orderByDesc('downloads')->limit(10)->get();
        });

        $sidebarTags = Cache::remember('sidebar_tags', 21600, function () {
            return Tag::withCount('posts')->orderByDesc('posts_count')->limit(20)->get();
        });

        $latestPosts = Post::published()->with('category')->latest('published_at')->limit(15)->get();

        return view('front.home', compact(
            'trending', 'latestSoftware',
            'latestApks', 'latestArticles', 'categories', 'sidebarTrending', 'sidebarTags',
            'latestPosts'
        ));
    }
}
