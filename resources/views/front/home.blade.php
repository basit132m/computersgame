@extends('layouts.app')

@section('title', 'تحميل العاب كمبيوتر وبرامج مجانية')
@section('meta_description', 'تحميل أفضل العاب الكمبيوتر والبرامج وتطبيقات الأندرويد مجاناً. روابط مباشرة وسريعة.')
@section('og_title', 'ألعاب الكمبيوتر - تحميل مجاني')

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "ألعاب الكمبيوتر",
    "url": "{{ config('app.url') }}",
    "description": "تحميل أفضل العاب الكمبيوتر والبرامج وتطبيقات الأندرويد مجاناً",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url('/search?q={search_term_string}') }}",
        "query-input": "required name=search_term_string"
    }
}
</script>
@endsection

@section('content')
<div class="flex gap-6">
    {{-- Main Content --}}
    <div class="flex-1 min-w-0">

        {{-- Hero Search Bar --}}
        <section class="bg-gradient-to-l from-blue-700 to-blue-900 rounded-2xl p-8 mb-8 text-white" aria-label="البحث الرئيسي">
            <h1 class="text-3xl font-bold mb-2 text-center">🎮 ألعاب الكمبيوتر</h1>
            <p class="text-blue-200 text-center mb-6">تحميل أفضل الألعاب والبرامج مجاناً</p>
            <div class="max-w-2xl mx-auto" x-data="liveSearch()">
                <div class="relative">
                    <input
                        type="text"
                        x-model="query"
                        @input.debounce.300ms="search()"
                        @focus="showResults = true"
                        @click.away="showResults = false"
                        placeholder="ابحث عن لعبة أو برنامج..."
                        class="w-full px-6 py-4 text-gray-900 rounded-xl text-lg focus:outline-none focus:ring-4 focus:ring-blue-300"
                        autocomplete="off"
                    >
                    <button @click="goSearch()" class="absolute left-3 top-1/2 -translate-y-1/2 bg-blue-700 text-white px-5 py-2 rounded-lg hover:bg-blue-800 transition font-medium">
                        بحث
                    </button>

                    {{-- Autocomplete Results --}}
                    <div x-show="showResults && results.length > 0"
                        class="absolute top-full mt-2 w-full bg-white rounded-xl shadow-xl z-50 overflow-hidden text-gray-900">
                        <template x-for="item in results" :key="item.url">
                            <a :href="item.url" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 transition border-b last:border-b-0">
                                <img :src="item.image || ''" :alt="item.title" class="w-12 h-8 object-cover rounded" x-show="item.image">
                                <div>
                                    <p class="font-medium text-sm" x-text="item.title"></p>
                                    <span class="text-xs text-blue-600" x-text="item.type"></span>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </section>

        {{-- Featured Categories --}}
        @if($categories->count())
        <section class="mb-8" aria-label="التصنيفات">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-1 h-6 bg-blue-700 rounded inline-block"></span>
                التصنيفات
            </h2>
            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                    class="bg-white rounded-xl p-4 text-center shadow-sm hover:shadow-md hover:text-blue-700 transition group">
                    <div class="text-3xl mb-2">
                        @if($category->image)
                            <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" class="w-10 h-10 mx-auto rounded">
                        @else
                            🗂️
                        @endif
                    </div>
                    <p class="text-sm font-medium text-gray-700 group-hover:text-blue-700">{{ $category->name }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $category->posts_count }} مقال</p>
                </a>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Trending This Week --}}
        @if($trending->count())
        <section class="mb-8" aria-label="الأكثر تحميلاً هذا الأسبوع">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-1 h-6 bg-red-500 rounded inline-block"></span>
                🔥 الأكثر تحميلاً هذا الأسبوع
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($trending->take(8) as $post)
                <x-front.post-card :post="$post" />
                @endforeach
            </div>
        </section>
        @endif

        {{-- Most Downloaded All Time --}}
        @if($mostDownloaded->count())
        <section class="mb-8" aria-label="الأكثر تحميلاً على الإطلاق">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-1 h-6 bg-yellow-500 rounded inline-block"></span>
                ⭐ الأكثر تحميلاً على الإطلاق
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($mostDownloaded as $post)
                <x-front.post-card :post="$post" />
                @endforeach
            </div>
        </section>
        @endif

        {{-- Latest Games --}}
        @if($latestGames->count())
        <section class="mb-8" aria-label="أحدث الألعاب">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-1 h-6 bg-green-500 rounded inline-block"></span>
                    🎮 أحدث الألعاب
                </h2>
                <a href="{{ route('category.show', 'games') }}" class="text-blue-700 text-sm hover:underline">عرض الكل ←</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($latestGames as $post)
                <x-front.post-card :post="$post" />
                @endforeach
            </div>
        </section>
        @endif

        {{-- Latest Software --}}
        @if($latestSoftware->count())
        <section class="mb-8" aria-label="أحدث البرامج">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-1 h-6 bg-purple-500 rounded inline-block"></span>
                    💻 أحدث البرامج
                </h2>
                <a href="{{ route('category.show', 'software') }}" class="text-blue-700 text-sm hover:underline">عرض الكل ←</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($latestSoftware as $post)
                <x-front.post-card :post="$post" />
                @endforeach
            </div>
        </section>
        @endif

        {{-- Latest APKs --}}
        @if($latestApks->count())
        <section class="mb-8" aria-label="أحدث تطبيقات الأندرويد">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-1 h-6 bg-green-600 rounded inline-block"></span>
                    📱 أحدث تطبيقات الأندرويد
                </h2>
                <a href="{{ route('category.show', 'android') }}" class="text-blue-700 text-sm hover:underline">عرض الكل ←</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($latestApks as $post)
                <x-front.post-card :post="$post" />
                @endforeach
            </div>
        </section>
        @endif

        {{-- Latest Articles --}}
        @if($latestArticles->count())
        <section class="mb-8" aria-label="أحدث المقالات والشروحات">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-1 h-6 bg-orange-500 rounded inline-block"></span>
                    📝 أحدث المقالات والشروحات
                </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($latestArticles as $post)
                <x-front.post-card :post="$post" />
                @endforeach
            </div>
        </section>
        @endif

    </div>

    {{-- Sidebar --}}
    <div class="hidden lg:block w-80 flex-shrink-0">
        <x-front.sidebar :trending="$sidebarTrending" :tags="$sidebarTags" />
    </div>
</div>
@endsection

@section('scripts')
<script>
function liveSearch() {
    return {
        query: '',
        results: [],
        showResults: false,
        async search() {
            if (this.query.length < 2) { this.results = []; return; }
            const res = await fetch(`/search/autocomplete?q=${encodeURIComponent(this.query)}`);
            this.results = await res.json();
            this.showResults = true;
        },
        goSearch() {
            if (this.query.trim()) {
                window.location.href = `/search?q=${encodeURIComponent(this.query)}`;
            }
        }
    }
}

function trackDownload(postId) {
    fetch('/download/click', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
        body: JSON.stringify({ post_id: postId, source: 'article_button' })
    });
}
</script>
@endsection
