<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            'site_name'              => 'ألعاب الكمبيوتر',
            'site_tagline'           => 'تحميل أفضل الألعاب والبرامج مجاناً',
            'site_email'             => 'info@computersgame.org',
            'site_url'               => 'https://computersgame.org',
            'site_logo'              => '',
            'site_favicon'           => '',
            'maintenance_mode'       => '0',

            // SEO
            'seo_home_title'         => 'ألعاب الكمبيوتر - تحميل أفضل الألعاب والبرامج مجاناً',
            'seo_home_description'   => 'موقع عربي متخصص في تحميل أفضل ألعاب الكمبيوتر والبرامج مجاناً. تغطية شاملة لألعاب الأكشن والمغامرة والرياضة.',
            'seo_home_keywords'      => 'تحميل العاب, العاب كمبيوتر, تحميل برامج, العاب مجانية, العاب اونلاين',

            // Open Graph
            'og_default_title'       => 'ألعاب الكمبيوتر',
            'og_default_description' => 'تحميل أفضل الألعاب والبرامج مجاناً',
            'og_default_image'       => '',

            // Schema.org
            'schema_org_type'        => 'Organization',
            'schema_org_name'        => 'ألعاب الكمبيوتر',

            // Verification
            'google_verification'    => '',
            'bing_verification'      => '',
            'google_analytics_id'    => '',
            'clarity_id'             => '',

            // Robots
            'robots_index'           => '1',
            'robots_follow'          => '1',
            'robots_archive'         => '1',

            // Social
            'social_facebook'        => '',
            'social_twitter'         => '',
            'social_youtube'         => '',
            'social_telegram'        => '',

            // API Keys
            'gemini_api_key'         => '',

            // Sitemap
            'sitemap_auto_generate'  => '1',

            // Downloads
            'download_countdown'     => '10',
            'download_page_ad'       => '1',
        ];

        foreach ($settings as $key => $value) {
            Setting::firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->command->info('✓ تم إنشاء الإعدادات الافتراضية');
    }
}
