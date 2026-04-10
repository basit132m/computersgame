@extends('layouts.admin')
@section('title', 'الإعدادات')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- General Settings --}}
        <div class="bg-white rounded-xl p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-gray-800 text-lg border-b pb-3">الإعدادات العامة</h3>
            @foreach([
                ['site_name', 'اسم الموقع', 'text', 'ألعاب الكمبيوتر'],
                ['site_description', 'وصف الموقع', 'textarea', 'تحميل العاب كمبيوتر وبرامج وتطبيقات اندرويد مجانا'],
                ['contact_email', 'البريد الإلكتروني', 'email', 'info@computersgame.org'],
                ['footer_text', 'نص التذييل', 'text', ''],
                ['posts_per_page', 'عدد المقالات في الصفحة', 'number', '12'],
            ] as [$key, $label, $type, $placeholder])
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                @if($type === 'textarea')
                <textarea name="{{ $key }}" rows="2"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $values[$key] ?? '' }}</textarea>
                @else
                <input type="{{ $type }}" name="{{ $key }}" value="{{ $values[$key] ?? '' }}"
                    placeholder="{{ $placeholder }}"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @endif
            </div>
            @endforeach

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">شعار الموقع</label>
                @if(!empty($values['logo']))
                <img src="{{ asset('storage/'.$values['logo']) }}" class="h-12 mb-2 rounded">
                @endif
                <input type="file" name="logo" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                @if(!empty($values['favicon']))
                <img src="{{ asset('storage/'.$values['favicon']) }}" class="w-8 h-8 mb-2">
                @endif
                <input type="file" name="favicon" accept="image/x-icon,image/png"
                    class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
        </div>

        {{-- Social & APIs --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-gray-800 text-lg border-b pb-3">روابط التواصل الاجتماعي</h3>
                @foreach([
                    ['facebook_url', 'فيسبوك', 'https://facebook.com/...'],
                    ['twitter_url', 'تويتر / X', 'https://twitter.com/...'],
                    ['telegram_url', 'تيليغرام', 'https://t.me/...'],
                    ['whatsapp_number', 'رقم واتساب', '9665XXXXXXXX'],
                ] as [$key, $label, $placeholder])
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                    <input type="text" name="{{ $key }}" value="{{ $values[$key] ?? '' }}"
                        placeholder="{{ $placeholder }}"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @endforeach
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-gray-800 text-lg border-b pb-3">مفاتيح API والتتبع</h3>
                @foreach([
                    ['gemini_api_key', 'مفتاح Gemini API', 'AIza...'],
                    ['adsense_publisher_id', 'معرف AdSense', 'ca-pub-...'],
                    ['google_analytics_id', 'Google Analytics ID', 'G-XXXXXXXXXX'],
                    ['clarity_id', 'Microsoft Clarity ID', 'xxxxxxxxxx'],
                ] as [$key, $label, $placeholder])
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                    <input type="text" name="{{ $key }}" value="{{ $values[$key] ?? '' }}"
                        placeholder="{{ $placeholder }}"
                        class="w-full px-3 py-2 border rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @endforeach

                <div class="flex items-center gap-3 pt-2 border-t">
                    <label class="text-sm font-medium text-gray-700">وضع الصيانة</label>
                    <input type="hidden" name="maintenance_mode" value="0">
                    <input type="checkbox" name="maintenance_mode" value="1"
                        {{ ($values['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}
                        class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-10 rounded-xl transition">
            حفظ جميع الإعدادات
        </button>
    </div>
</form>
@endsection
