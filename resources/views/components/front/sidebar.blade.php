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

    {{-- بانرات مميزة --}}
    @php $sideBanners = \App\Models\SidebarBanner::active()->get(); @endphp
    @if($sideBanners->count())
    <div class="space-y-2">
        @foreach($sideBanners as $banner)
        <a href="{{ $banner->url }}" class="block relative rounded-lg overflow-hidden group" style="aspect-ratio:341/179;">
            <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                 loading="lazy">
            @if($banner->title)
            <div class="absolute bottom-0 inset-x-0 bg-red-600 px-3 py-1.5">
                <p class="text-white text-xs font-bold text-center leading-snug truncate">{{ $banner->title }}</p>
            </div>
            @endif
        </a>
        @endforeach
    </div>
    @endif


</aside>
