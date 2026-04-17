<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $trending = Cache::remember('home_trending', 3600, function () {
            return Post::published()
                ->withCount(['downloadClicks as weekly_downloads' => function ($q) {
                    $q->whereBetween('clicked_at', [now()->startOfWeek(), now()->endOfWeek()]);
                }])
                ->orderByDesc('weekly_downloads')
                ->limit(10)
                ->get();
        });

        $latestSoftware = Cache::remember('home_latest_software', 3600, function () {
            return Post::published()->ofType('software')->latest('published_at')->limit(8)->get();
        });

        $latestApks = Cache::remember('home_latest_apks', 3600, function () {
            return Post::published()->ofType('apk')->latest('published_at')->limit(6)->get();
        });

        $latestArticles = Cache::remember('home_latest_articles', 3600, function () {
            return Post::published()
                ->whereIn('type', ['blog', 'tutorial'])
                ->latest('published_at')
                ->limit(6)
                ->get();
        });

        $categories = Cache::remember('home_categories', 21600, function () {
            return Category::whereNull('parent_id')->withCount('posts')->orderBy('sort_order')->limit(12)->get();
        });

        $sidebarTrending = Cache::remember('sidebar_trending', 3600, function () {
            return Post::published()->orderByDesc('downloads')->limit(10)->get();
        });

        $sidebarTags = Cache::remember('sidebar_tags', 21600, function () {
            return \App\Models\Tag::withCount('posts')->orderByDesc('posts_count')->limit(20)->get();
        });

        $latestPosts = Cache::remember('home_latest_all', 1800, function () {
            return Post::published()->latest('published_at')->limit(15)->get();
        });

        return view('front.home', compact(
            'trending', 'latestSoftware',
            'latestApks', 'latestArticles', 'categories', 'sidebarTrending', 'sidebarTags',
            'latestPosts'
        ));
    }
}
