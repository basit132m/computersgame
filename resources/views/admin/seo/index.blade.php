@extends('layouts.admin')
@section('title', 'إعدادات SEO')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.seo.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- General SEO --}}
        <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
            <h3 class="font-bold text-gray-800 pb-2 border-b">إعدادات SEO العامة</h3>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الصفحة الرئيسية (Title)</label>
                    <input type="text" name="seo_home_title" value="{{ old('seo_home_title', $settings['seo_home_title'] ?? '') }}"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="ألعاب الكمبيوتر - تحميل أفضل الألعاب والبرامج">
                    <p class="text-xs text-gray-400 mt-1">الحد المثالي: 50-60 حرفاً</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">وصف الصفحة الرئيسية (Meta Description)</label>
                    <textarea name="seo_home_description" rows="2"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="موقع عربي لتحميل أفضل الألعاب والبرامج مجاناً...">{{ old('seo_home_description', $settings['seo_home_description'] ?? '') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">الحد المثالي: 150-160 حرفاً</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الكلمات المفتاحية العامة</label>
                    <input type="text" name="seo_home_keywords" value="{{ old('seo_home_keywords', $settings['seo_home_keywords'] ?? '') }}"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="تحميل العاب, العاب كمبيوتر, تحميل برامج">
                </div>
            </div>
        </div>

        {{-- Open Graph --}}
        <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
            <h3 class="font-bold text-gray-800 pb-2 border-b">Open Graph (مشاركة على السوشيال ميديا)</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">عنوان OG الافتراضي</label>
                    <input type="text" name="og_default_title" value="{{ old('og_default_title', $settings['og_default_title'] ?? '') }}"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">صورة OG الافتراضية (رابط)</label>
                    <input type="url" name="og_default_image" value="{{ old('og_default_image', $settings['og_default_image'] ?? '') }}"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                        placeholder="https://computersgame.org/og-image.jpg">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وصف OG الافتراضي</label>
                <textarea name="og_default_description" rows="2"
                    class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('og_default_description', $settings['og_default_description'] ?? '') }}</textarea>
            </div>
        </div>

        {{-- Schema.org --}}
        <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
            <h3 class="font-bold text-gray-800 pb-2 border-b">Schema.org / JSON-LD</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">نوع المنظمة</label>
                    <select name="schema_org_type" class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Organization" {{ ($settings['schema_org_type'] ?? '') === 'Organization' ? 'selected' : '' }}>Organization</option>
                        <option value="WebSite" {{ ($settings['schema_org_type'] ?? '') === 'WebSite' ? 'selected' : '' }}>WebSite</option>
                        <option value="Blog" {{ ($settings['schema_org_type'] ?? '') === 'Blog' ? 'selected' : '' }}>Blog</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">اسم المنظمة</label>
                    <input type="text" name="schema_org_name" value="{{ old('schema_org_name', $settings['schema_org_name'] ?? '') }}"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- Verification Codes --}}
        <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
            <h3 class="font-bold text-gray-800 pb-2 border-b">أكواد التحقق</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Google Search Console</label>
                    <input type="text" name="google_verification" value="{{ old('google_verification', $settings['google_verification'] ?? '') }}"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                        placeholder="google-site-verification=...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bing Webmaster</label>
                    <input type="text" name="bing_verification" value="{{ old('bing_verification', $settings['bing_verification'] ?? '') }}"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Google Analytics ID</label>
                <input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}"
                    class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                    placeholder="G-XXXXXXXXXX">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Microsoft Clarity ID</label>
                <input type="text" name="clarity_id" value="{{ old('clarity_id', $settings['clarity_id'] ?? '') }}"
                    class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm">
            </div>
        </div>

        {{-- Robots Meta --}}
        <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
            <h3 class="font-bold text-gray-800 pb-2 border-b">إعدادات Robots</h3>
            <div class="grid grid-cols-3 gap-4">
                <label class="flex items-center gap-2 cursor-pointer p-3 border rounded-xl hover:bg-gray-50">
                    <input type="checkbox" name="robots_index" value="1" {{ ($settings['robots_index'] ?? 1) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600">
                    <div>
                        <p class="text-sm font-medium text-gray-700">index</p>
                        <p class="text-xs text-gray-400">السماح بالفهرسة</p>
                    </div>
                </label>
                <label class="flex items-center gap-2 cursor-pointer p-3 border rounded-xl hover:bg-gray-50">
                    <input type="checkbox" name="robots_follow" value="1" {{ ($settings['robots_follow'] ?? 1) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600">
                    <div>
                        <p class="text-sm font-medium text-gray-700">follow</p>
                        <p class="text-xs text-gray-400">تتبع الروابط</p>
                    </div>
                </label>
                <label class="flex items-center gap-2 cursor-pointer p-3 border rounded-xl hover:bg-gray-50">
                    <input type="checkbox" name="robots_archive" value="1" {{ ($settings['robots_archive'] ?? 1) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600">
                    <div>
                        <p class="text-sm font-medium text-gray-700">archive</p>
                        <p class="text-xs text-gray-400">حفظ نسخة cache</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-8 rounded-xl transition">حفظ الإعدادات</button>
        </div>
    </form>
</div>
@endsection
