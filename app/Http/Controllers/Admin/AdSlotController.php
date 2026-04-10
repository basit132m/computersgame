<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdSlot;
use Illuminate\Http\Request;

class AdSlotController extends Controller
{
    private array $slots = [
        'header_ad'         => 'إعلان رأس الصفحة (تحت القائمة)',
        'sidebar_ad'        => 'إعلان الشريط الجانبي',
        'in_content_ad_1'   => 'إعلان داخل المحتوى (بعد 3 فقرات)',
        'in_content_ad_2'   => 'إعلان داخل المحتوى (بعد المتطلبات)',
        'before_download_ad' => 'إعلان قبل زر التحميل',
        'timer_ad_top'      => 'إعلان أعلى صفحة التحميل',
        'timer_ad_bottom'   => 'إعلان أسفل صفحة التحميل',
        'footer_ad'         => 'إعلان أعلى التذييل',
    ];

    public function index()
    {
        $adSlots = collect($this->slots)->map(function ($label, $key) {
            $slot = AdSlot::where('slot_key', $key)->first();
            return [
                'key'    => $key,
                'label'  => $label,
                'code'   => $slot?->code,
                'active' => $slot?->active ?? false,
                'id'     => $slot?->id,
            ];
        });
        return view('admin.ad-slots.index', compact('adSlots'));
    }

    public function update(Request $request, string $key)
    {
        AdSlot::updateOrCreate(
            ['slot_key' => $key],
            [
                'label'  => $this->slots[$key] ?? $key,
                'code'   => $request->code,
                'active' => $request->boolean('active'),
            ]
        );
        return back()->with('success', 'تم حفظ الإعلان');
    }
}
