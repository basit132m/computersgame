<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\DownloadClick;
use App\Models\Post;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function show(Post $post)
    {
        if ($post->status !== 'published') {
            abort(404);
        }

        $post->load('downloadLinks');

        return view('front.download', compact('post'));
    }

    public function trackClick(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'link_id' => 'nullable|exists:download_links,id',
            'source'  => 'required|in:article_button,timer_page',
        ]);

        DownloadClick::create([
            'post_id'    => $request->post_id,
            'link_id'    => $request->link_id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer'    => $request->headers->get('referer'),
            'source'     => $request->source,
            'clicked_at' => now(),
        ]);

        // Increment post downloads count when clicked on article
        if ($request->source === 'article_button') {
            Post::where('id', $request->post_id)->increment('downloads');
        }

        // Increment link click count
        if ($request->link_id) {
            \App\Models\DownloadLink::where('id', $request->link_id)->increment('clicks');
        }

        return response()->json(['success' => true]);
    }
}
