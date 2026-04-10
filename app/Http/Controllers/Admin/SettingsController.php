<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    private array $settings = [
        'site_name'           => 'اسم الموقع',
        'site_description'    => 'وصف الموقع',
        'contact_email'       => 'البريد الإلكتروني للتواصل',
        'facebook_url'        => 'رابط فيسبوك',
        'twitter_url'         => 'رابط تويتر',
        'whatsapp_number'     => 'رقم واتساب',
        'telegram_url'        => 'رابط قناة تيليغرام',
        'footer_text'         => 'نص التذييل',
        'gemini_api_key'      => 'مفتاح API جيميني',
        'adsense_publisher_id' => 'معرف ناشر AdSense',
        'google_analytics_id' => 'معرف Google Analytics',
        'clarity_id'          => 'معرف Microsoft Clarity',
        'default_robots'      => 'القيمة الافتراضية لـ Robots',
        'posts_per_page'      => 'عدد المقالات في الصفحة',
        'maintenance_mode'    => 'وضع الصيانة',
        'recaptcha_site_key'  => 'مفتاح reCAPTCHA',
        'recaptcha_secret_key' => 'المفتاح السري reCAPTCHA',
    ];

    public function index()
    {
        $values = [];
        foreach (array_keys($this->settings) as $key) {
            $values[$key] = Setting::get($key);
        }
        return view('admin.settings.index', compact('values'));
    }

    public function update(Request $request)
    {
        foreach (array_keys($this->settings) as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('site', 'public');
            Setting::set('logo', $path);
        }
        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('site', 'public');
            Setting::set('favicon', $path);
        }

        Cache::flush();
        return back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }
}
