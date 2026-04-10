@props(['post'])

<article class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition group">
    <a href="{{ url($post->slug) }}" class="block">
        <div class="relative overflow-hidden aspect-video bg-gray-200">
            @if($post->featured_image)
            <img
                src="{{ \App\Services\ImageService::getThumbUrl($post->featured_image) }}"
                alt="{{ $post->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                loading="lazy"
                width="400"
                height="225"
            >
            @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-100 to-blue-200">
                <span class="text-4xl">🎮</span>
            </div>
            @endif
            {{-- Type Badge --}}
            <span class="absolute top-2 right-2 bg-blue-700 text-white text-xs px-2 py-1 rounded-full font-medium">
                {{ $post->type_ar }}
            </span>
        </div>
    </a>
    <div class="p-4">
        <a href="{{ url($post->slug) }}">
            <h3 class="font-bold text-gray-900 line-clamp-2 hover:text-blue-700 transition mb-2">{{ $post->title }}</h3>
        </a>
        <div class="flex flex-wrap gap-2 text-xs text-gray-500 mb-3">
            @if($post->version)
            <span class="flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                v{{ $post->version }}
            </span>
            @endif
            @if($post->file_size)
            <span>{{ $post->file_size }}</span>
            @endif
            <span>{{ $post->platform_ar }}</span>
        </div>
        @if($post->excerpt)
        <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ strip_tags($post->excerpt) }}</p>
        @endif
        <a href="{{ route('download.show', $post->slug) }}" target="_blank"
            onclick="trackDownload({{ $post->id }})"
            class="block w-full text-center bg-blue-700 hover:bg-blue-800 text-white text-sm font-bold py-2 px-4 rounded-lg transition">
            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            تحميل مجاني
        </a>
    </div>
</article>
