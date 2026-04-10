@extends('layouts.admin')
@section('title', 'إدارة الإعلانات')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">مواقع الإعلانات</h3>
            <span class="text-xs text-gray-400">استخدم المفاتيح في قالب Blade بـ &#64;adslot('key')</span>
        </div>

        <div class="divide-y">
            @forelse($adSlots as $slot)
            <div class="p-5">
                <form action="{{ route('admin.ad-slots.update', $slot['key']) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $slot['label'] }}</h4>
                            <code class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded">&#64;adslot('{{ $slot['key'] }}')</code>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="active" value="1" {{ $slot['active'] ? 'checked' : '' }}
                                    class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-600">مفعّل</span>
                            </label>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-1.5 rounded-lg transition">
                                حفظ
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">كود الإعلان (AdSense أو أي HTML)</label>
                        <textarea name="code" rows="4"
                            class="w-full px-3 py-2 border rounded-xl text-xs font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50"
                            placeholder="الصق كود AdSense هنا...">{{ $slot['code'] }}</textarea>
                    </div>
                </form>
            </div>
            @empty
            <div class="px-4 py-12 text-center text-gray-400">
                <p class="mb-3">لا توجد مواقع إعلانية.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Instructions --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
        <h4 class="font-semibold text-blue-800 mb-2">كيفية استخدام مواقع الإعلانات</h4>
        <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
            <li>في أي قالب Blade، استخدم: <code class="bg-blue-100 px-1 rounded">&#64;adslot('sidebar_ad')</code></li>
            <li>إذا كان الموقع غير مفعّل أو فارغاً، لن يُعرض أي كود</li>
            <li>المواقع المتاحة: header_ad, sidebar_ad, in_content_ad_1, in_content_ad_2, before_download_ad, timer_ad_top, timer_ad_bottom, footer_ad</li>
        </ul>
    </div>
</div>
@endsection
