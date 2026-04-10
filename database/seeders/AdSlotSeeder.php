<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdSlotSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [
            ['slot_key' => 'header_ad',      'label' => 'إعلان الرأس (Header)',         'code' => '', 'active' => false],
            ['slot_key' => 'sidebar_ad',     'label' => 'إعلان الشريط الجانبي',         'code' => '', 'active' => false],
            ['slot_key' => 'in_content_ad',  'label' => 'إعلان داخل المحتوى',           'code' => '', 'active' => false],
            ['slot_key' => 'after_post_ad',  'label' => 'إعلان بعد المقال',             'code' => '', 'active' => false],
            ['slot_key' => 'footer_ad',      'label' => 'إعلان التذييل (Footer)',        'code' => '', 'active' => false],
            ['slot_key' => 'download_ad',    'label' => 'إعلان صفحة التحميل',           'code' => '', 'active' => false],
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
