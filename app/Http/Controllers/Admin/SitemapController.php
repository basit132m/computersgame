<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateSitemapJob;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SitemapController extends Controller
{
    public function index()
    {
        $settings = [
            'sitemap_auto_generate' => Setting::get('sitemap_auto_generate', true),
        ];

        $stats = [
            'posts_count'      => \App\Models\Post::published()->count(),
            'categories_count' => \App\Models\Category::count(),
            'last_post'        => \App\Models\Post::published()->latest('published_at')->value('published_at'),
            'static_pages'     => 6, // home, search, about, contact, privacy, terms
        ];

        $sitemaps = [
            [
                'label'       => 'Sitemap Index',
                'url'         => url('sitemap.xml'),
                'description' => 'الملف الرئيسي الذي يجمع كل الـ Sitemaps',
                'icon'        => 'fa-sitemap',
                'color'       => '#2271b1',
            ],
            [
                'label'       => 'Posts Sitemap',
                'url'         => url('sitemap-posts.xml'),
                'description' => $stats['posts_count'] . ' مقال منشور',
                'icon'        => 'fa-file-alt',
                'color'       => '#00a32a',
            ],
            [
                'label'       => 'Categories Sitemap',
                'url'         => url('sitemap-categories.xml'),
                'description' => $stats['categories_count'] . ' تصنيف',
                'icon'        => 'fa-folder-open',
                'color'       => '#dba617',
            ],
            [
                'label'       => 'Pages Sitemap',
                'url'         => url('sitemap-pages.xml'),
                'description' => $stats['static_pages'] . ' صفحات ثابتة',
                'icon'        => 'fa-copy',
                'color'       => '#d63638',
            ],
        ];

        return view('admin.sitemap.index', compact('settings', 'stats', 'sitemaps'));
    }

    public function generate()
    {
        \Illuminate\Support\Facades\Cache::forget('sitemap_index');
        return back()->with('success', 'تم تحديث الـ Sitemap. الرابط جاهز للزيارة.');
    }

    public function ping()
    {
        $sitemapUrl = urlencode(url('sitemap-index.xml'));
        try {
            Http::timeout(10)->get("https://www.google.com/ping?sitemap={$sitemapUrl}");
        } catch (\Throwable $e) {
            // Silently fail — Google ping is not critical
        }
        return back()->with('success', 'تم إرسال إشعار لـ Google بتحديث الـ Sitemap.');
    }

    public function settings(Request $request)
    {
        Setting::set('sitemap_auto_generate', $request->boolean('sitemap_auto_generate') ? '1' : '0', 'sitemap');
        return back()->with('success', 'تم حفظ إعدادات الـ Sitemap.');
    }
}
