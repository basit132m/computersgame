@props(['trending' => [], 'tags' => []])

<aside class="space-y-6">
    {{-- Search Box --}}
    <div class="bg-white rounded-xl shadow-sm p-4">
        <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            البحث
        </h3>
        <form action="{{ route('search') }}" method="GET">
            <div class="flex gap-2">
                <input type="text" name="q" placeholder="ابحث هنا..."
                    class="flex-1 px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </form>
    </div>

    {{-- Sidebar Ad --}}
    @adslot('sidebar_ad')

    {{-- Categories --}}
    <div class="bg-white rounded-xl shadow-sm p-4">
        <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            التصنيفات
        </h3>
        <ul class="space-y-1">
            @foreach(\App\Models\Category::whereNull('parent_id')->withCount('posts')->orderBy('sort_order')->get() as $cat)
            <li>
                <a href="{{ route('category.show', $cat->slug) }}"
                    class="flex items-center justify-between py-1.5 px-2 rounded hover:bg-blue-50 hover:text-blue-700 transition text-sm">
                    <span>{{ $cat->name }}</span>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $cat->posts_count }}</span>
                </a>
            </li>
            @endforeach
        </ul>
    </div>

    {{-- Trending --}}
    @if(count($trending))
    <div class="bg-white rounded-xl shadow-sm p-4">
        <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            الأكثر تحميلاً
        </h3>
        <ol class="space-y-2">
            @foreach($trending as $i => $post)
            <li class="flex items-start gap-3">
                <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-700 rounded-full text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                <a href="{{ url($post->slug) }}" class="text-sm text-gray-700 hover:text-blue-700 transition line-clamp-2">{{ $post->title }}</a>
            </li>
            @endforeach
        </ol>
    </div>
    @endif

    {{-- Tags --}}
    @if(count($tags))
    <div class="bg-white rounded-xl shadow-sm p-4">
        <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            الوسوم
        </h3>
        <div class="flex flex-wrap gap-2">
            @foreach($tags as $tag)
            <a href="{{ route('tag.show', $tag->slug) }}"
                class="bg-gray-100 hover:bg-blue-100 hover:text-blue-700 text-gray-600 text-xs px-3 py-1.5 rounded-full transition">
                {{ $tag->name }} <span class="text-gray-400">({{ $tag->posts_count }})</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</aside>
