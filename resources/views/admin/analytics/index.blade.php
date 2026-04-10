@extends('layouts.admin')
@section('title', 'التحليلات')

@section('content')
<div class="space-y-6">
    {{-- Top Downloaded --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            الأكثر تحميلاً (إجمالي)
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right px-4 py-2 font-semibold text-gray-600">#</th>
                        <th class="text-right px-4 py-2 font-semibold text-gray-600">العنوان</th>
                        <th class="text-right px-4 py-2 font-semibold text-gray-600">النوع</th>
                        <th class="text-right px-4 py-2 font-semibold text-gray-600">التحميلات</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($topDownloaded as $i => $post)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-gray-500 font-mono">{{ $i + 1 }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="font-medium text-gray-800 hover:text-blue-700">{{ $post->title }}</a>
                        </td>
                        <td class="px-4 py-2"><span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">{{ $post->type_ar }}</span></td>
                        <td class="px-4 py-2 font-bold text-gray-800">{{ number_format($post->downloads) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Download Source Breakdown --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="font-bold text-gray-800 mb-4">مصادر التحميل</h2>
        <div class="flex gap-6">
            @foreach($sourceBreakdown as $source)
            <div class="text-center">
                <p class="text-3xl font-bold text-gray-800">{{ number_format($source->count) }}</p>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $source->source === 'article_button' ? 'من صفحة المقال' : 'من صفحة التحميل' }}
                </p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Daily Downloads Chart (last 30 days) --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="font-bold text-gray-800 mb-4">التحميلات اليومية (آخر 30 يوم)</h2>
        <div class="overflow-x-auto">
            <div class="flex items-end gap-1 h-32 min-w-max">
                @php
                    $maxCount = $dailyDownloads->max('count') ?: 1;
                @endphp
                @foreach($dailyDownloads as $day)
                <div class="flex flex-col items-center gap-1 w-6">
                    <div class="bg-blue-500 rounded-t hover:bg-blue-600 transition cursor-default"
                        style="height: {{ ($day->count / $maxCount * 100) }}%; min-height: 2px; width: 100%"
                        title="{{ $day->date }}: {{ $day->count }}"></div>
                    <span class="text-xs text-gray-400 rotate-90 origin-center w-8 hidden sm:block" style="font-size: 0.6rem">{{ substr($day->date, 5) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Broken Link Checker --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="font-bold text-gray-800 mb-4">فاحص الروابط المعطوبة</h2>
        @livewire('broken-link-checker')
    </div>
</div>
@endsection
