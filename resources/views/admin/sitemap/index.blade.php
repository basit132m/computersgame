@extends('layouts.admin')
@section('title', 'خريطة الموقع')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    {{-- Sitemap Status --}}
    <div class="bg-white rounded-xl p-5 shadow-sm">
        <h3 class="font-bold text-gray-800 mb-4">حالة خريطة الموقع</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-500 mb-1">آخر تحديث</p>
                <p class="font-semibold text-gray-800">
                    @if(file_exists(public_path('sitemap-index.xml')))
                        {{ \Carbon\Carbon::createFromTimestamp(filemtime(public_path('sitemap-index.xml')))->diffForHumans() }}
                    @else
                        <span class="text-red-500">لم يُنشأ بعد</span>
                    @endif
                </p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-500 mb-1">حالة الملف</p>
                <p class="font-semibold">
                    @if(file_exists(public_path('sitemap-index.xml')))
                        <span class="text-green-600">✓ موجود</span>
                    @else
                        <span class="text-red-500">✗ غير موجود</span>
                    @endif
                </p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-500 mb-1">رابط الـ Sitemap</p>
                <a href="/sitemap-index.xml" target="_blank" class="text-blue-600 hover:underline text-sm font-mono break-all">
                    /sitemap-index.xml
                </a>
            </div>
        </div>
    </div>

    {{-- Generate --}}
    <div class="bg-white rounded-xl p-5 shadow-sm">
        <h3 class="font-bold text-gray-800 mb-2">إنشاء خريطة الموقع</h3>
        <p class="text-sm text-gray-500 mb-4">سيتم إنشاء ملفات sitemap-index.xml و sitemap-posts.xml و sitemap-categories.xml و sitemap-pages.xml في مجلد public.</p>
        <div class="flex gap-3">
            <form action="{{ route('admin.sitemap.generate') }}" method="POST">
                @csrf
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-6 rounded-xl transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    إنشاء الآن
                </button>
            </form>
            <form action="{{ route('admin.sitemap.ping') }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-xl transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    إرسال إشعار لـ Google
                </button>
            </form>
        </div>
    </div>

    {{-- Sitemap Files --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b bg-gray-50">
            <h3 class="font-bold text-gray-800">ملفات Sitemap</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">الملف</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">الحجم</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">تاريخ التعديل</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @php
                    $sitemapFiles = ['sitemap-index.xml', 'sitemap-posts.xml', 'sitemap-categories.xml', 'sitemap-pages.xml'];
                @endphp
                @foreach($sitemapFiles as $file)
                @php $path = public_path($file); @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="font-mono text-blue-600">{{ $file }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">
                        @if(file_exists($path))
                            {{ number_format(filesize($path) / 1024, 1) }} KB
                        @else
                            <span class="text-red-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500">
                        @if(file_exists($path))
                            {{ \Carbon\Carbon::createFromTimestamp(filemtime($path))->format('Y/m/d H:i') }}
                        @else
                            <span class="text-red-400">غير موجود</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if(file_exists($path))
                            <a href="/{{ $file }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50 transition">عرض</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Auto Generation Settings --}}
    <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
        <h3 class="font-bold text-gray-800 pb-2 border-b">الإنشاء التلقائي</h3>
        <form action="{{ route('admin.sitemap.settings') }}" method="POST">
            @csrf @method('PUT')
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div>
                    <p class="font-medium text-gray-700">إنشاء تلقائي عند نشر مقال</p>
                    <p class="text-xs text-gray-400 mt-0.5">سيتم تحديث الـ Sitemap تلقائياً عند نشر أو تعديل أي مقال</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="sitemap_auto_generate" value="1" class="sr-only peer"
                        {{ ($settings['sitemap_auto_generate'] ?? true) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            <div class="mt-3">
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-medium py-2 px-6 rounded-xl transition text-sm">حفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection
