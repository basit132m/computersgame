<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;

class TagController extends Controller
{
    public function show(Tag $tag)
    {
        $posts = $tag->posts()->published()->latest('published_at')->paginate(12);

        $sidebarTrending = Cache::remember('sidebar_trending', 3600, function () {
            return \App\Models\Post::published()->orderByDesc('downloads')->limit(10)->get();
        });

        return view('front.tag', compact('tag', 'posts', 'sidebarTrending'));
    }
}
