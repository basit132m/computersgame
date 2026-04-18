<?php

namespace App\Providers;

use App\Models\AdSlot;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\App\Services\GeminiService::class);
    }

    public function boot(): void
    {
        // AdSlot Blade directive
        Blade::directive('adslot', function ($expression) {
            return "<?php
                \$_adCode = \\App\\Models\\AdSlot::getCode($expression);
                if (\$_adCode): echo \$_adCode; endif;
            ?>";
        });

        // Share nav categories with all views (cached — cleared when categories change)
        View::composer('*', function ($view) {
            static $navCategories = null;
            if ($navCategories === null) {
                try {
                    $navCategories = Cache::remember('nav_categories', 21600, function () {
                        return Category::whereNull('parent_id')->orderBy('sort_order')->limit(16)->get();
                    });
                } catch (\Exception $e) {
                    $navCategories = collect();
                }
            }
            $view->with('navCategories', $navCategories);
        });

        // Share global site settings with all views
        View::composer('*', function ($view) {
            static $settings = null;
            if ($settings === null) {
                try {
                    $settings = [
                        'site_name'        => Setting::get('site_name', 'ألعاب الكمبيوتر'),
                        'site_description' => Setting::get('site_description', 'تحميل العاب كمبيوتر وبرامج وتطبيقات اندرويد مجانا'),
                        'logo'             => Setting::get('logo'),
                        'favicon'          => Setting::get('favicon'),
                        'footer_text'      => Setting::get('footer_text', '© ' . date('Y') . ' ألعاب الكمبيوتر - جميع الحقوق محفوظة'),
                        'facebook_url'     => Setting::get('facebook_url'),
                        'twitter_url'      => Setting::get('twitter_url'),
                        'telegram_url'     => Setting::get('telegram_url'),
                        'whatsapp_number'  => Setting::get('whatsapp_number'),
                        'analytics_id'     => Setting::get('google_analytics_id'),
                        'clarity_id'       => Setting::get('clarity_id'),
                    ];
                } catch (\Exception $e) {
                    $settings = [
                        'site_name'        => 'ألعاب الكمبيوتر',
                        'site_description' => 'تحميل العاب كمبيوتر وبرامج وتطبيقات اندرويد مجانا',
                        'logo'             => null,
                        'favicon'          => null,
                        'footer_text'      => '© ' . date('Y') . ' ألعاب الكمبيوتر',
                        'facebook_url'     => null,
                        'twitter_url'      => null,
                        'telegram_url'     => null,
                        'whatsapp_number'  => null,
                        'analytics_id'     => null,
                        'clarity_id'       => null,
                    ];
                }
            }
            $view->with('globalSettings', $settings);
        });
    }
}
