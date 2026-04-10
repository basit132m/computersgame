<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\DownloadClick;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'total_posts'       => Post::count(),
                'published_posts'   => Post::where('status', 'published')->count(),
                'total_downloads_today' => DownloadClick::whereDate('clicked_at', today())->count(),
                'pending_comments'  => Comment::where('status', 'pending')->count(),
            ];
        });

        $topDownloaded = Cache::remember('admin_top_downloaded_week', 300, function () {
            return Post::published()
                ->withCount(['downloadClicks as weekly_downloads' => function ($q) {
                    $q->whereBetween('clicked_at', [now()->startOfWeek(), now()->endOfWeek()]);
                }])
                ->orderByDesc('weekly_downloads')
                ->limit(5)
                ->get();
        });

        $recentComments = Comment::with('post')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'topDownloaded', 'recentComments'));
    }
}
