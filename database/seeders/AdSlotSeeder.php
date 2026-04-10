<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdSlotSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [
            ['key' => 'header_ad',      'name' => 'إعلان الرأس (Header)',         'code' => '', 'is_active' => false],
            ['key' => 'sidebar_ad',     'name' => 'إعلان الشريط الجانبي',         'code' => '', 'is_active' => false],
            ['key' => 'in_content_ad',  'name' => 'إعلان داخل المحتوى',           'code' => '', 'is_active' => false],
            ['key' => 'after_post_ad',  'name' => 'إعلان بعد المقال',             'code' => '', 'is_active' => false],
            ['key' => 'footer_ad',      'name' => 'إعلان التذييل (Footer)',        'code' => '', 'is_active' => false],
            ['key' => 'download_ad',    'name' => 'إعلان صفحة التحميل',           'code' => '', 'is_active' => false],
        ];

        foreach ($slots as $slot) {
            DB::table('ad_slots')->insertOrIgnore([
                ...$slot,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ تم إنشاء مواقع الإعلانات');
    }
}
