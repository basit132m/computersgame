@extends('layouts.admin')
@section('title', 'إدارة الإعلانات')

@section('content')
<div class="space-y-6">

    {{-- ═══════════════════════════════════
         DIRECT LINK BUTTONS (download ads)
    ═══════════════════════════════════ --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b bg-gray-50 flex items-center gap-3">
            <i class="fas fa-link text-blue-600"></i>
            <div>
                <h3 class="font-bold text-gray-800">روابط مباشرة — Direct Links</h3>
                <p class="text-xs text-gray-400 mt-0.5">روابط الإعلان المستخدمة في أزرار التحميل. النقرة الأولى تفتح هذا الرابط، والثانية تفتح الرابط الفعلي.</p>
            </div>
        </div>

        <div class="divide-y">
            @foreach($directLinkSlots as $slot)
            <div class="p-5">
                <form action="{{ route('admin.ad-slots.update', $slot['key']) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-mouse-pointer text-blue-500 text-sm"></i>
                                {{ $slot['label'] }}
                            </h4>
                            <code class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded mt-1 inline-block">{{ $slot['key'] }}</code>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="active" value="1" {{ $slot['active'] ? 'checked' : '' }}
                                    class="w-4 h-4 rounded text-blue-600">
                                <span class="text-sm text-gray-600">مفعّل</span>
                            </label>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-1.5 rounded-lg transition">
                                حفظ
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">
                            الرابط المباشر — Direct URL
                            <span class="text-gray-400">(اتركه فارغاً لتعطيل الميزة)</span>
                        </label>
                        <input type="url" name="code" value="{{ $slot['code'] }}"
                            placeholder="https://example.com/ad-landing-page"
                            class="w-full px-3 py-2 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </form>
                <div class="mt-3 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-xs text-amber-700">
                    @if($slot['key'] === 'download_btn_direct_url')
                    <strong>كيف يعمل:</strong> يظهر زر إعلاني أسفل زر التحميل في صفحة المقال.
                    النقرة الأولى للزائر تفتح الرابط المباشر في تبويب جديد،
                    والنقرة الثانية تنقله إلى صفحة التحميل.
                    @else
                    <strong>كيف يعمل:</strong> في صفحة التحميل (بعد التايمر)،
                    النقرة الأولى على أي زر تفتح الرابط المباشر في تبويب جديد،
                    والنقرة الثانية تفتح رابط التحميل الفعلي.
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ═══════════════════════════════════
         REGULAR AD SLOTS (HTML/AdSense)
    ═══════════════════════════════════ --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b bg-gray-50 flex items-center gap-3">
            <i class="fas fa-ad text-green-600"></i>
            <div>
                <h3 class="font-bold text-gray-800">مواقع الإعلانات — Ad Slots</h3>
                <p class="text-xs text-gray-400 mt-0.5">استخدم <code class="bg-gray-100 px-1 rounded">&#64;adslot('key')</code> في قوالب Blade</p>
            </div>
        </div>

        <div class="divide-y">
            @foreach($adSlots as $slot)
            <div class="p-5">
                <form action="{{ route('admin.ad-slots.update', $slot['key']) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $slot['label'] }}</h4>
                            <code class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded mt-1 inline-block">&#64;adslot('{{ $slot['key'] }}')</code>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="active" value="1" {{ $slot['active'] ? 'checked' : '' }}
                                    class="w-4 h-4 rounded text-blue-600">
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
            @endforeach
        </div>
    </div>

</div>
@endsection
