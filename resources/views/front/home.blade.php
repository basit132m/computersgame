@extends('layouts.app')

@section('title', 'تحميل العاب كمبيوتر وبرامج مجانية')
@section('meta_description', 'تحميل أفضل العاب الكمبيوتر والبرامج وتطبيقات الأندرويد مجاناً. روابط مباشرة وسريعة.')
@section('og_title', 'ألعاب الكمبيوتر - تحميل مجاني')
@section('og_description', 'تحميل أفضل العاب الكمبيوتر والبرامج وتطبيقات الأندرويد مجاناً. روابط مباشرة وسريعة.')

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "{{ $globalSettings['site_name'] ?? 'ألعاب الكمبيوتر' }}",
    "url": "{{ config('app.url') }}",
    "description": "{{ $globalSettings['site_description'] ?? 'تحميل أفضل العاب الكمبيوتر والبرامج وتطبيقات الأندرويد مجاناً' }}",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url('/search?q={search_term_string}') }}",
        "query-input": "required name=search_term_string"
    }
}
</script>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "{{ $globalSettings['site_name'] ?? 'ألعاب الكمبيوتر' }}",
    "url": "{{ config('app.url') }}",
    @if(!empty($globalSettings['logo']))
    "logo": "{{ asset('storage/'.$globalSettings['logo']) }}",
    @endif
    "sameAs": [
        @if(!empty($globalSettings['facebook_url']))"{{ $globalSettings['facebook_url'] }}"@endif
        @if(!empty($globalSettings['twitter_url'])),{{ '"'.$globalSettings['twitter_url'].'"' }}@endif
        @if(!empty($globalSettings['youtube_url'])),{{ '"'.$globalSettings['youtube_url'].'"' }}@endif
        @if(!empty($globalSettings['telegram_url'])),{{ '"'.$globalSettings['telegram_url'].'"' }}@endif
    ]
}
</script>
@endsection

@section('content')
<div class="flex gap-5">

    {{-- ═══════════════════════
         Main Content (RIGHT in RTL)
    ═══════════════════════ --}}
    <div class="flex-1 min-w-0">

        {{-- H1: SEO only, hidden visually --}}
        <h1 class="sr-only">
            تحميل العاب كمبيوتر وبرامج وتطبيقات اندرويد مجاناً
        </h1>

        {{-- Header Ad --}}
        @adslot('header_ad')

        {{-- Latest Posts List --}}
        <div class="mb-5">
            <div class="flex items-center justify-between px-1 py-2 mb-3">
                <h2 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-clock text-[#30A38A] text-sm"></i>
                    أحدث الإضافات
                </h2>
                <a href="{{ route('search') }}" class="text-xs text-[#30A38A] hover:underline">
                    عرض الكل <i class="fas fa-angle-left text-xs"></i>
                </a>
            </div>
            <div class="space-y-3">
                @forelse($latestPosts as $post)
                    <x-front.post-card :post="$post" variant="list" :priority="$loop->index < 3" />
                @empty
                    <div class="py-12 text-center text-gray-400 bg-white border border-gray-200 rounded-lg">
                        <i class="fas fa-inbox text-4xl mb-3 block"></i>
                        <p>لا توجد مقالات بعد</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- In-Content Ad --}}
        @adslot('in_content_ad')

        {{-- Latest Software --}}
        @if($latestSoftware->count())
        <div class="mb-5">
            <div class="flex items-center justify-between px-1 py-2 mb-3">
                <h2 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-laptop text-[#30A38A] text-sm"></i>
                    أحدث البرامج
                </h2>
                <a href="{{ route('category.show', 'software') }}" class="text-xs text-[#30A38A] hover:underline">
                    عرض الكل <i class="fas fa-angle-left text-xs"></i>
                </a>
            </div>
            <div class="space-y-3">
                @foreach($latestSoftware as $post)
                    <x-front.post-card :post="$post" variant="list" />
                @endforeach
            </div>
        </div>
        @endif

        {{-- Latest APKs --}}
        @if($latestApks->count())
        <div class="mb-5">
            <div class="flex items-center justify-between px-1 py-2 mb-3">
                <h2 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-mobile-alt text-[#30A38A] text-sm"></i>
                    تطبيقات الأندرويد
                </h2>
                <a href="{{ route('category.show', 'android') }}" class="text-xs text-[#30A38A] hover:underline">
                    عرض الكل <i class="fas fa-angle-left text-xs"></i>
                </a>
            </div>
            <div class="space-y-3">
                @foreach($latestApks as $post)
                    <x-front.post-card :post="$post" variant="list" />
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- ═══════════════════════
         Sidebar (LEFT in RTL)
    ═══════════════════════ --}}
    <div class="hidden lg:block w-96 flex-shrink-0" style="position:sticky;top:1rem;align-self:flex-start;">
        <x-front.sidebar :trending="$sidebarTrending" :tags="$sidebarTags" />
    </div>

</div>
@endsection

@section('scripts')
<script>
function trackDownload(postId) {
    fetch('/download/click', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
        },
        body: JSON.stringify({ post_id: postId, source: 'home_list' })
    });
}
</script>
@endsection
