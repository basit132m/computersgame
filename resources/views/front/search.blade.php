@extends('layouts.app')

@section('title', 'نتائج البحث' . ($query ? ' عن: ' . $query : ''))
@section('robots', 'noindex, follow')

@section('content')
<div class="flex gap-5">

    {{-- Main Content --}}
    <div class="flex-1 min-w-0">

        {{-- Breadcrumb --}}
        <nav class="mb-3 text-xs text-gray-500 flex items-center gap-1">
            <a href="{{ url('/') }}" class="hover:text-[#30A38A] transition">
                <i class="fas fa-home text-[10px]"></i> الرئيسية
            </a>
            <i class="fas fa-angle-left text-[10px] text-gray-400"></i>
            <span class="text-gray-700 font-medium">البحث</span>
        </nav>

        {{-- Search Box --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                <i class="fas fa-search text-[#30A38A]"></i>
                <h1 class="font-bold text-gray-900 text-sm">
                    @if($query)
                        نتائج البحث عن: <span class="text-[#30A38A]">{{ $query }}</span>
                    @else
                        البحث في الموقع
                    @endif
                </h1>
            </div>
            <div class="p-4">
                <form action="{{ route('search') }}" method="GET" x-data="liveSearch()">
                    <div class="flex gap-2 mb-3">
                        <input type="text" name="q"
                               x-model="query"
                               @input.debounce.300ms="search()"
                               value="{{ $query }}"
                               placeholder="ابحث عن لعبة أو برنامج..."
                               class="flex-1 px-4 py-2.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-[#30A38A]"
                               autocomplete="off">
                        <button type="submit"
                                class="bg-[#30A38A] hover:bg-[#268a74] text-white px-6 py-2.5 rounded text-sm font-bold transition flex-shrink-0">
                            <i class="fas fa-search"></i> بحث
                        </button>
                    </div>
                    {{-- Type Filters --}}
                    <div class="flex flex-wrap gap-2 items-center">
                        <span class="text-xs text-gray-500">التصفية:</span>
                        @foreach(['game' => 'لعبة', 'software' => 'برنامج', 'apk' => 'تطبيق', 'blog' => 'مقال'] as $key => $label)
                        <a href="{{ route('search', ['q' => $query, 'type' => $key]) }}"
                           class="text-xs px-3 py-1.5 rounded-full border transition {{ $type === $key ? 'bg-[#30A38A] text-white border-[#30A38A]' : 'border-gray-300 text-gray-600 hover:border-[#30A38A] hover:text-[#30A38A]' }}">
                            {{ $label }}
                        </a>
                        @endforeach
                        @if($type)
                        <a href="{{ route('search', ['q' => $query]) }}"
                           class="text-xs px-3 py-1.5 rounded-full border border-red-200 text-red-500 hover:bg-red-50 transition">
                            <i class="fas fa-times ml-1"></i> إزالة الفلتر
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Results --}}
        @if($query)
            @if($posts->count())
            <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 text-xs text-gray-500">
                    تم العثور على <strong class="text-gray-800">{{ $posts->total() }}</strong> نتيجة
                </div>
                <div class="px-4">
                    @foreach($posts as $post)
                    <x-front.post-card :post="$post" variant="list" />
                    @endforeach
                </div>
            </div>
            <div class="mb-4">
                {{ $posts->links('vendor.pagination.arabic') }}
            </div>
            @else
            <div class="bg-white border border-gray-200 rounded p-12 text-center text-gray-400">
                <i class="fas fa-search text-5xl mb-3 block"></i>
                <p class="text-lg font-medium text-gray-600 mb-1">لم يتم العثور على نتائج</p>
                <p class="text-sm">جرب كلمات بحث مختلفة أو تصفح التصنيفات</p>
                <a href="{{ url('/') }}"
                   class="mt-4 inline-block bg-[#30A38A] text-white px-6 py-2 rounded text-sm hover:bg-[#268a74] transition">
                    تصفح الرئيسية
                </a>
            </div>
            @endif
        @else
        <div class="bg-white border border-gray-200 rounded p-12 text-center text-gray-400">
            <i class="fas fa-search text-5xl mb-3 block"></i>
            <p>ابحث عن لعبتك أو برنامجك المفضل</p>
        </div>
        @endif

    </div>

    {{-- Sidebar --}}
    <div class="hidden lg:block w-72 flex-shrink-0" style="position:sticky;top:1rem;align-self:flex-start;">
        <x-front.sidebar :trending="[]" :tags="[]" />
    </div>

</div>
@endsection

@section('scripts')
<script>
function liveSearch() {
    return {
        query: '{{ $query }}',
        results: [],
        showResults: false,
        async search() {
            if (this.query.length < 2) { this.results = []; return; }
            const res = await fetch(`/search/autocomplete?q=${encodeURIComponent(this.query)}`);
            this.results = await res.json();
        }
    }
}
</script>
@endsection
