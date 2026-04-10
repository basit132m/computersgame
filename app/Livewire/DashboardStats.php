<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\DownloadClick;
use App\Models\Post;
use Livewire\Attributes\Polling;
use Livewire\Component;

#[Polling('60s')]
class DashboardStats extends Component
{
    public function render()
    {
        $stats = [
            'total_posts'           => Post::count(),
            'published_posts'       => Post::where('status', 'published')->count(),
            'total_downloads_today' => DownloadClick::whereDate('clicked_at', today())->count(),
            'pending_comments'      => Comment::where('status', 'pending')->count(),
            'total_downloads_week'  => DownloadClick::whereBetween('clicked_at', [now()->startOfWeek(), now()])->count(),
        ];

        $topDownloaded = Post::published()
            ->withCount(['downloadClicks as weekly_downloads' => function ($q) {
                $q->whereBetween('clicked_at', [now()->startOfWeek(), now()->endOfWeek()]);
            }])
            ->orderByDesc('weekly_downloads')
            ->limit(5)
            ->get();

        return view('livewire.dashboard-stats', compact('stats', 'topDownloaded'));
    }
}
