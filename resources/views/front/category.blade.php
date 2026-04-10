@extends('layouts.app')

@section('title', ($category->meta_title ?: $category->name) . ' - تحميل مجاني')
@section('meta_description', $category->meta_description ?: $category->description)
@section('canonical', route('category.show', $category->slug))

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
<div class="flex gap-6">
    <div class="flex-1 min-w-0">
        {{-- Breadcrumb --}}
        <nav aria-label="مسار التنقل" class="mb-4 text-sm text-gray-500">
            <ol class="flex items-center gap-2">
                <li><a href="{{ url('/') }}" class="hover:text-blue-700">الرئيسية</a></li>
                <li class="before:content-['/'] before:mx-1 font-medium text-gray-800">{{ $category->name }}</li>
            </ol>
        </nav>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
        @if($category->description)
        <p class="text-gray-600 mb-6">{{ $category->description }}</p>
        @endif

        {{-- Filters --}}
        <form method="GET" class="bg-white rounded-xl border p-4 mb-6 flex flex-wrap gap-4 items-center">
            <div>
                <label class="text-sm text-gray-600 block mb-1">المنصة</label>
                <select name="platform" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">الكل</option>
                    <option value="pc" {{ request('platform') === 'pc' ? 'selected' : '' }}>كمبيوتر</option>
                    <option value="android" {{ request('platform') === 'android' ? 'selected' : '' }}>أندرويد</option>
                    <option value="ios" {{ request('platform') === 'ios' ? 'selected' : '' }}>iOS</option>
                    <option value="mac" {{ request('platform') === 'mac' ? 'selected' : '' }}>ماك</option>
                </select>
            </div>
            <div>
                <label class="text-sm text-gray-600 block mb-1">الترتيب</label>
                <select name="sort" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>الأحدث</option>
                    <option value="downloads" {{ request('sort') === 'downloads' ? 'selected' : '' }}>الأكثر تحميلاً</option>
                    <option value="views" {{ request('sort') === 'views' ? 'selected' : '' }}>الأكثر مشاهدة</option>
                </select>
            </div>
            <button type="submit" class="mt-5 bg-blue-700 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-800 transition">
                تصفية
            </button>
        </form>

        {{-- Posts Grid --}}
        @if($posts->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @foreach($posts as $post)
            <x-front.post-card :post="$post" />
            @endforeach
        </div>

        {{-- Pagination --}}
        {{ $posts->links('vendor.pagination.arabic') }}
        @else
        <div class="text-center py-16 text-gray-500">
            <p class="text-4xl mb-4">📭</p>
            <p class="text-lg">لا توجد مقالات في هذا التصنيف بعد</p>
        </div>
        @endif
    </div>

    <div class="hidden lg:block w-80 flex-shrink-0">
        <x-front.sidebar :trending="$sidebarTrending" :tags="$sidebarTags" />
    </div>
</div>
@endsection
