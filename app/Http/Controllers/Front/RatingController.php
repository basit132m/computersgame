<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'score'   => 'required|integer|min:1|max:5',
        ]);

        Rating::updateOrCreate(
            ['post_id' => $request->post_id, 'ip_address' => $request->ip()],
            ['score' => $request->score]
        );

        $post = Post::find($request->post_id);
        $avg  = round($post->ratings()->avg('score'), 1);
        $cnt  = $post->ratings()->count();

        return response()->json([
            'success'    => true,
            'avg'        => $avg,
            'count'      => $cnt,
            'message'    => 'شكراً لتقييمك!',
        ]);
    }
}
