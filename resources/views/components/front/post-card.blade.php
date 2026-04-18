@props(['post', 'variant' => 'grid'])

@if($variant === 'list')
{{-- Standalone card: title on top, image + excerpt below --}}
<article class="bg-white border border-gray-200 rounded-lg overflow-hidden group">
    {{-- Title --}}
    <div class="px-4 pt-4 pb-3 border-b border-gray-100">
        <a href="{{ url($post->slug) }}">
            <h3 class="font-bold text-gray-900 text-base leading-snug hover:text-[#30A38A] transition text-center">
                {{ $post->title }}
            </h3>
        </a>
    </div>
    {{-- Image + Excerpt --}}
    <div class="flex gap-4 p-4">
        {{-- Image — right side in RTL --}}
        <a href="{{ url($post->slug) }}" class="flex-shrink-0">
            @if($post->featured_image)
            <img src="{{ \App\Services\ImageService::getThumbUrl($post->featured_image) }}"
                 alt="{{ $post->title }}"
                 class="w-36 h-24 object-cover rounded border border-gray-200 group-hover:opacity-90 transition"
                 loading="lazy" width="144" height="96">
            @else
            <div class="w-36 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded flex items-center justify-center">
                <i class="fas fa-gamepad text-gray-400 text-2xl"></i>
            </div>
            @endif
        </a>
        {{-- Excerpt --}}
        <div class="flex-1 min-w-0">
            @if($post->excerpt)
            <p class="text-sm text-gray-500 line-clamp-3 leading-relaxed">{{ strip_tags($post->excerpt) }}</p>
            @endif
        </div>
    </div>
</article>
@else
{{-- Grid card (used in category / search pages) --}}
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
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                <i class="fas fa-gamepad text-gray-400 text-4xl"></i>
            </div>
            @endif
            <span class="absolute top-2 right-2 bg-[#30A38A] text-white text-xs px-2 py-1 rounded-full font-medium">
                {{ $post->type_ar }}
            </span>
        </div>
    </a>
    <div class="p-4">
        <a href="{{ url($post->slug) }}">
            <h3 class="font-bold text-gray-900 line-clamp-2 hover:text-[#30A38A] transition mb-2">{{ $post->title }}</h3>
        </a>
        <div class="flex flex-wrap gap-2 text-xs text-gray-500 mb-3">
            @if($post->version)
            <span>v{{ $post->version }}</span>
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
            class="block w-full text-center bg-[#30A38A] hover:bg-[#268a74] text-white text-sm font-bold py-2 px-4 rounded-lg transition">
            <i class="fas fa-download ml-1"></i> تحميل مجاني
        </a>
    </div>
</article>
@endif
