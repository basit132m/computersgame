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
        return view('admin.sitemap.index', compact('settings'));
    }

    public function generate()
    {
        GenerateSitemapJob::dispatch();
        return back()->with('success', 'جاري إنشاء خريطة الموقع في الخلفية، انتظر لحظة ثم أعد التحميل.');
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
