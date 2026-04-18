@extends('layouts.app')

@section('title', ($category->meta_title ?: $category->name) . ' - تحميل مجاني')
@section('meta_description', $category->meta_description ?: $category->description)
@section('canonical', request()->has('page') ? url()->full() : route('category.show', $category->slug))

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {"@type": "ListItem", "position": 1, "name": "الرئيسية", "item": "{{ url('/') }}"},
        {"@type": "ListItem", "position": 2, "name": "{{ $category->name }}", "item": "{{ route('category.show', $category->slug) }}"}
    ]
}
</script>
@endsection

@section('content')
<div class="flex gap-5">

    {{-- Main Content --}}
    <div class="flex-1 min-w-0">

        {{-- Breadcrumb --}}
        <nav aria-label="مسار التنقل" class="mb-3 text-xs text-gray-500 flex flex-wrap items-center gap-1">
            <a href="{{ url('/') }}" class="hover:text-[#30A38A] transition">
                <i class="fas fa-home text-[10px]"></i> الرئيسية
            </a>
            <i class="fas fa-angle-left text-[10px] text-gray-400"></i>
            <span class="text-gray-700 font-medium">{{ $category->name }}</span>
        </nav>

        {{-- Category Header --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                <i class="fas fa-folder-open text-[#30A38A]"></i>
                <h1 class="font-bold text-gray-900">{{ $category->name }}</h1>
            </div>
            @if($category->description)
            <p class="px-4 py-3 text-sm text-gray-600">{{ $category->description }}</p>
            @endif

            {{-- Filters --}}
            <form method="GET" class="px-4 pb-3 flex flex-wrap gap-3 items-end">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">المنصة</label>
                    <select name="platform"
                            class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#30A38A]">
                        <option value="">الكل</option>
                        <option value="pc"      {{ request('platform') === 'pc'      ? 'selected' : '' }}>كمبيوتر</option>
                        <option value="android" {{ request('platform') === 'android' ? 'selected' : '' }}>أندرويد</option>
                        <option value="ios"     {{ request('platform') === 'ios'     ? 'selected' : '' }}>iOS</option>
                        <option value="mac"     {{ request('platform') === 'mac'     ? 'selected' : '' }}>ماك</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">الترتيب</label>
                    <select name="sort"
                            class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#30A38A]">
                        <option value="latest"    {{ request('sort', 'latest') === 'latest'    ? 'selected' : '' }}>الأحدث</option>
                        <option value="downloads" {{ request('sort') === 'downloads'            ? 'selected' : '' }}>الأكثر تحميلاً</option>
                        <option value="views"     {{ request('sort') === 'views'                ? 'selected' : '' }}>الأكثر مشاهدة</option>
                    </select>
                </div>
                <button type="submit"
                        class="bg-[#30A38A] hover:bg-[#268a74] text-white px-5 py-2 rounded text-sm font-bold transition">
                    <i class="fas fa-filter ml-1"></i> تصفية
                </button>
            </form>
        </div>

        {{-- Posts List --}}
        @if($posts->count())
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
            <div class="px-4">
                @foreach($posts as $post)
                <x-front.post-card :post="$post" variant="list" />
                @endforeach
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mb-4">
            {{ $posts->links('vendor.pagination.arabic') }}
        </div>
        @else
        <div class="bg-white border border-gray-200 rounded p-12 text-center text-gray-400">
            <i class="fas fa-inbox text-5xl mb-3 block"></i>
            <p class="text-lg">لا توجد مقالات في هذا التصنيف بعد</p>
        </div>
        @endif

    </div>

    {{-- Sidebar --}}
    <div class="hidden lg:block w-72 flex-shrink-0">
        <x-front.sidebar :trending="$sidebarTrending" :tags="$sidebarTags" />
    </div>

</div>
@endsection
