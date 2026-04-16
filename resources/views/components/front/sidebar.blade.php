@props(['trending' => [], 'tags' => []])

<aside class="space-y-4">

    {{-- أقسام الموقع --}}
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="flex items-center gap-2 px-3 py-2.5 bg-gray-50 border-b border-gray-200">
            <span class="w-7 h-7 rounded-full bg-[#30A38A] flex items-center justify-center flex-shrink-0">
                <i class="fas fa-th text-white text-xs"></i>
            </span>
            <h3 class="font-bold text-gray-900 text-sm">أقسام الموقع</h3>
        </div>
        <div class="grid grid-cols-2 gap-2 p-3">
            @foreach(\App\Models\Category::whereNull('parent_id')->orderBy('sort_order')->limit(16)->get() as $cat)
            <a href="{{ route('category.show', $cat->slug) }}"
               class="flex items-center gap-1.5 border border-gray-300 rounded px-2 py-2 text-xs text-gray-700 hover:border-[#30A38A] hover:text-[#30A38A] transition truncate">
                <i class="fas fa-play-circle text-gray-400 text-xs flex-shrink-0"></i>
                <span class="truncate">{{ $cat->name }}</span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Sidebar Ad --}}
    @adslot('sidebar_ad')

    {{-- الأكثر تحميلاً --}}
    @if(count($trending))
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="flex items-center gap-2 px-3 py-2.5 bg-gray-50 border-b border-gray-200">
            <span class="w-7 h-7 rounded-full bg-[#30A38A] flex items-center justify-center flex-shrink-0">
                <i class="fas fa-fire text-white text-xs"></i>
            </span>
            <h3 class="font-bold text-gray-900 text-sm">الأكثر تحميلاً</h3>
        </div>
        <ol class="divide-y divide-gray-100">
            @foreach($trending as $i => $post)
            <li>
                <a href="{{ url($post->slug) }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 transition group">
                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-[#30A38A] text-white text-xs font-bold flex items-center justify-center">
                        {{ $i + 1 }}
                    </span>
                    <span class="text-xs text-gray-700 group-hover:text-[#30A38A] transition line-clamp-2 leading-relaxed">
                        {{ $post->title }}
                    </span>
                </a>
            </li>
            @endforeach
        </ol>
    </div>
    @endif

</aside>
