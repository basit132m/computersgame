<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Request $request, Category $category)
    {
        $query = $category->posts()->published();

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'downloads' => $query->orderByDesc('downloads'),
            'views'     => $query->orderByDesc('views'),
            default     => $query->orderByDesc('published_at'),
        };

        $posts = $query->paginate(12)->withQueryString();

        $sidebarTrending = Post::published()->orderByDesc('downloads')->limit(10)->get();

        $sidebarTags = Tag::withCount('posts')->orderByDesc('posts_count')->limit(20)->get();

        return view('front.category', compact('category', 'posts', 'sidebarTrending', 'sidebarTags'));
    }
}
