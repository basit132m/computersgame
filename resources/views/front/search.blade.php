@extends('layouts.app')

@section('title', 'نتائج البحث' . ($query ? ' عن: ' . $query : ''))
@section('robots', 'noindex, follow')

@section('content')
<div class="flex gap-6">
    <div class="flex-1 min-w-0">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">
            @if($query)
                نتائج البحث عن: <span class="text-blue-700">{{ $query }}</span>
            @else
                البحث
            @endif
        </h1>

        {{-- Search Form --}}
        <form action="{{ route('search') }}" method="GET" class="mb-6" x-data="liveSearch()">
            <div class="relative">
                <input
                    type="text"
                    name="q"
                    x-model="query"
                    @input.debounce.300ms="search()"
                    @focus="showResults = true"
                    @click.away="showResults = false"
                    value="{{ $query }}"
                    placeholder="ابحث عن لعبة أو برنامج..."
                    class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl text-lg focus:outline-none focus:border-blue-500"
                    autocomplete="off"
                >
                <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 bg-blue-700 text-white px-6 py-2.5 rounded-lg hover:bg-blue-800 transition">
                    بحث
                </button>
            </div>

            {{-- Type Filter --}}
            <div class="mt-3 flex flex-wrap gap-2">
                <span class="text-sm text-gray-600 ml-2">التصفية:</span>
                @foreach(['game' => 'لعبة', 'software' => 'برنامج', 'apk' => 'تطبيق', 'blog' => 'مقال'] as $key => $label)
                <a href="{{ route('search', ['q' => $query, 'type' => $key]) }}"
                    class="text-xs px-3 py-1.5 rounded-full border transition {{ $type === $key ? 'bg-blue-700 text-white border-blue-700' : 'bg-white text-gray-600 hover:border-blue-500' }}">
                    {{ $label }}
                </a>
                @endforeach
                @if($type)
                <a href="{{ route('search', ['q' => $query]) }}" class="text-xs px-3 py-1.5 rounded-full bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition">
                    ✕ إزالة الفلتر
                </a>
                @endif
            </div>
        </form>

        {{-- Results --}}
        @if($query)
            @if($posts->count())
                <p class="text-gray-600 mb-4">تم العثور على {{ $posts->total() }} نتيجة</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    @foreach($posts as $post)
                    <x-front.post-card :post="$post" />
                    @endforeach
                </div>
                {{ $posts->links('vendor.pagination.arabic') }}
            @else
            <div class="text-center py-16">
                <p class="text-4xl mb-4">🔍</p>
                <p class="text-xl text-gray-600">لم يتم العثور على نتائج لـ "{{ $query }}"</p>
                <p class="text-gray-400 mt-2">جرب كلمات بحث مختلفة أو تصفح التصنيفات</p>
                <a href="{{ url('/') }}" class="mt-4 inline-block bg-blue-700 text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition">
                    تصفح الرئيسية
                </a>
            </div>
            @endif
        @else
        <div class="text-center py-16 text-gray-500">
            <p class="text-4xl mb-4">🔍</p>
            <p>ابحث عن لعبتك أو برنامجك المفضل</p>
        </div>
        @endif
    </div>

    <div class="hidden lg:block w-80 flex-shrink-0">
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
        },
        goSearch() {
            if (this.query.trim()) {
                window.location.href = `/search?q=${encodeURIComponent(this.query)}`;
            }
        }
    }
}
</script>
@endsection
