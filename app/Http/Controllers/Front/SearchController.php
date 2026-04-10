<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $type  = $request->get('type', '');

        $posts = collect();
        if (strlen($query) >= 2) {
            $q = Post::published()
                ->where(function ($builder) use ($query) {
                    $builder->where('title', 'like', "%{$query}%")
                        ->orWhere('excerpt', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%");
                });

            if ($type) {
                $q->where('type', $type);
            }

            $posts = $q->orderByDesc('views')->paginate(12)->withQueryString();
        }

        return view('front.search', compact('posts', 'query', 'type'));
    }

    public function autocomplete(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = Post::published()
            ->where('title', 'like', "%{$query}%")
            ->select('title', 'slug', 'type', 'featured_image')
            ->limit(8)
            ->get()
            ->map(function ($post) {
                return [
                    'title' => $post->title,
                    'url'   => url($post->slug),
                    'type'  => $post->type_ar,
                    'image' => $post->featured_image ? asset('storage/' . $post->featured_image) : null,
                ];
            });

        return response()->json($results);
    }
}
