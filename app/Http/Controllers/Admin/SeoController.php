<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function index()
    {
        $keys = [
            'seo_home_title', 'seo_home_description', 'seo_home_keywords',
            'og_default_title', 'og_default_description', 'og_default_image',
            'schema_org_type', 'schema_org_name',
            'google_verification', 'bing_verification',
            'google_analytics_id', 'clarity_id',
            'robots_index', 'robots_follow', 'robots_archive',
        ];

        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = Setting::get($key);
        }

        return view('admin.seo.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $fields = [
            'seo_home_title', 'seo_home_description', 'seo_home_keywords',
            'og_default_title', 'og_default_description', 'og_default_image',
            'schema_org_type', 'schema_org_name',
            'google_verification', 'bing_verification',
            'google_analytics_id', 'clarity_id',
        ];

        foreach ($fields as $field) {
            Setting::set($field, $request->input($field, ''), 'seo');
        }

        // Checkboxes (absent when unchecked)
        Setting::set('robots_index',   $request->boolean('robots_index') ? '1' : '0', 'seo');
        Setting::set('robots_follow',  $request->boolean('robots_follow') ? '1' : '0', 'seo');
        Setting::set('robots_archive', $request->boolean('robots_archive') ? '1' : '0', 'seo');

        return back()->with('success', 'تم حفظ إعدادات SEO بنجاح.');
    }
}
