@extends('layouts.admin')
@section('title', 'خريطة الموقع — Sitemap')

@section('content')
<div class="space-y-6">

    {{-- ═══════════════════════
         SITEMAP CARDS
    ═══════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach($sitemaps as $sm)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: {{ $sm['color'] }}1a;">
                    <i class="fas {{ $sm['icon'] }} text-lg" style="color: {{ $sm['color'] }};"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-gray-800 text-sm">{{ $sm['label'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $sm['description'] }}</p>
                </div>
            </div>
            <div class="border-t border-gray-100 px-5 py-2 bg-gray-50 flex items-center justify-between gap-2">
                <code class="text-xs text-gray-500 truncate">{{ $sm['url'] }}</code>
                <a href="{{ $sm['url'] }}" target="_blank"
                   class="flex-shrink-0 text-xs font-medium px-3 py-1 rounded-lg border transition"
                   style="color:{{ $sm['color'] }}; border-color:{{ $sm['color'] }}40;"
                   onmouseover="this.style.background='{{ $sm['color'] }}'; this.style.color='#fff';"
                   onmouseout="this.style.background=''; this.style.color='{{ $sm['color'] }}';">
                    <i class="fas fa-external-link-alt text-xs ml-1"></i> فتح
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ═══════════════════════
         STATS + ACTIONS
    ═══════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Stats --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-blue-600 text-sm"></i>
                    إحصائيات — Stats
                </h3>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-x-reverse divide-gray-100">
                <div class="px-5 py-5 text-center">
                    <p class="text-3xl font-black text-blue-600">{{ number_format($stats['posts_count']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">مقال منشور<br><span class="text-gray-400">Published Posts</span></p>
                </div>
                <div class="px-5 py-5 text-center">
                    <p class="text-3xl font-black text-yellow-500">{{ number_format($stats['categories_count']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">تصنيف<br><span class="text-gray-400">Categories</span></p>
                </div>
                <div class="px-5 py-5 text-center">
                    <p class="text-3xl font-black text-red-500">{{ $stats['static_pages'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">صفحات ثابتة<br><span class="text-gray-400">Static Pages</span></p>
                </div>
                <div class="px-5 py-5 text-center">
                    <p class="text-xl font-black text-green-600">
                        {{ $stats['last_post'] ? $stats['last_post']->format('M d') : '—' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">آخر نشر<br><span class="text-gray-400">Last Published</span></p>
                </div>
            </div>
            {{-- Robots.txt hint --}}
            <div class="border-t border-gray-100 px-5 py-3 bg-gray-50">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-info-circle text-blue-400 ml-1"></i>
                    أضف هذا السطر إلى ملف <code class="bg-gray-100 px-1 rounded">robots.txt</code>:
                    <code class="text-blue-600 mr-2">Sitemap: {{ url('sitemap.xml') }}</code>
                </p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="space-y-4">

            {{-- Refresh cache --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h4 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
                    <i class="fas fa-sync-alt text-blue-600 text-sm"></i>
                    تحديث الـ Cache
                    <span class="text-xs font-normal text-gray-400">Refresh Cache</span>
                </h4>
                <p class="text-xs text-gray-500 mb-4">يمسح كاش الـ Sitemap Index ليتم إعادة بنائه عند أول زيارة.</p>
                <form action="{{ route('admin.sitemap.generate') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg transition text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-sync-alt"></i>
                        تحديث الآن
                    </button>
                </form>
            </div>

            {{-- Ping Google --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h4 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
                    <i class="fab fa-google text-red-500 text-sm"></i>
                    إشعار Google
                    <span class="text-xs font-normal text-gray-400">Ping Google</span>
                </h4>
                <p class="text-xs text-gray-500 mb-4">يرسل إشعاراً لـ Google بأن الـ Sitemap تم تحديثه.</p>
                <form action="{{ route('admin.sitemap.ping') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 px-4 rounded-lg transition text-sm flex items-center justify-center gap-2">
                        <i class="fab fa-google"></i>
                        إرسال لـ Google
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════
         SITEMAP URLS TABLE
    ═══════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-link text-green-600 text-sm"></i>
                روابط Sitemap — Sitemap URLs
            </h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-right px-5 py-3 font-semibold text-gray-500 text-xs uppercase">الملف — File</th>
                    <th class="text-right px-5 py-3 font-semibold text-gray-500 text-xs uppercase">الرابط — URL</th>
                    <th class="text-right px-5 py-3 font-semibold text-gray-500 text-xs uppercase">المحتوى — Content</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($sitemaps as $sm)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <i class="fas {{ $sm['icon'] }} text-sm" style="color:{{ $sm['color'] }};"></i>
                            <span class="font-semibold text-gray-800">{{ $sm['label'] }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <code class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded">{{ $sm['url'] }}</code>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $sm['description'] }}</td>
                    <td class="px-5 py-3 text-left">
                        <a href="{{ $sm['url'] }}" target="_blank"
                           class="text-xs font-medium text-blue-600 hover:text-blue-800 px-3 py-1.5 rounded-lg border border-blue-200 hover:bg-blue-50 transition">
                            <i class="fas fa-external-link-alt text-xs ml-1"></i> فتح
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ═══════════════════════
         AUTO GENERATION
    ═══════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-robot text-purple-600 text-sm"></i>
            الإنشاء التلقائي — Auto Generation
        </h3>
        <form action="{{ route('admin.sitemap.settings') }}" method="POST">
            @csrf @method('PUT')
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200 mb-4">
                <div>
                    <p class="font-medium text-gray-700">تحديث تلقائي عند نشر مقال</p>
                    <p class="text-xs text-gray-400 mt-0.5">Auto-regenerate sitemap cache when a post is published or updated</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="sitemap_auto_generate" value="1" class="sr-only peer"
                        {{ ($settings['sitemap_auto_generate'] ?? true) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-300
                                peer-checked:bg-blue-600
                                after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                                after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full"></div>
                </label>
            </div>
            <button type="submit"
                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded-lg transition text-sm">
                حفظ الإعدادات
            </button>
        </form>
    </div>

</div>
@endsection
