<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DownloadClick;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $topDownloaded = Post::published()
            ->orderByDesc('downloads')
            ->limit(10)
            ->get(['id', 'title', 'slug', 'downloads', 'type']);

        $dailyDownloads = DownloadClick::select(
            DB::raw('DATE(clicked_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('clicked_at', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $sourceBreakdown = DownloadClick::select('source', DB::raw('COUNT(*) as count'))
            ->groupBy('source')
            ->get();

        return view('admin.analytics.index', compact('topDownloaded', 'dailyDownloads', 'sourceBreakdown'));
    }
}
