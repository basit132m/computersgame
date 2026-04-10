<div>
    {{-- Filters --}}
    <div class="bg-white rounded-xl p-4 mb-4 flex flex-wrap gap-3 shadow-sm">
        <div class="flex items-center gap-2 flex-1 min-w-48">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="بحث في المقالات..."
                class="flex-1 border-0 focus:ring-0 text-sm outline-none"
            >
            <div wire:loading wire:target="search" class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        </div>

        <select wire:model.live="type" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">جميع الأنواع</option>
            <option value="game">لعبة</option>
            <option value="software">برنامج</option>
            <option value="apk">تطبيق</option>
            <option value="blog">مقال</option>
            <option value="tutorial">شرح</option>
            <option value="listicle">قائمة</option>
            <option value="review">مراجعة</option>
        </select>

        <select wire:model.live="status" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">جميع الحالات</option>
            <option value="draft">مسودة</option>
            <option value="pending">معلق</option>
            <option value="published">منشور</option>
            <option value="scheduled">مجدول</option>
        </select>

        @if($search || $type || $status)
        <button wire:click="$set('search', ''); $set('type', ''); $set('status', '')"
            class="text-gray-400 hover:text-red-500 text-sm px-3 py-2 rounded-lg hover:bg-red-50 transition flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            مسح
        </button>
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">
                            <button wire:click="sort('title')" class="flex items-center gap-1 hover:text-blue-700">
                                العنوان
                                @if($sortBy === 'title')
                                    <svg class="w-3 h-3 {{ $sortDir === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                @endif
                            </button>
                        </th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">النوع</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">التصنيف</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden lg:table-cell">
                            <button wire:click="sort('views')" class="flex items-center gap-1 hover:text-blue-700">
                                المشاهدات
                                @if($sortBy === 'views')<svg class="w-3 h-3 {{ $sortDir === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>@endif
                            </button>
                        </th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden lg:table-cell">
                            <button wire:click="sort('downloads')" class="flex items-center gap-1 hover:text-blue-700">
                                التحميلات
                                @if($sortBy === 'downloads')<svg class="w-3 h-3 {{ $sortDir === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>@endif
                            </button>
                        </th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($posts as $post)
                    <tr class="hover:bg-gray-50" wire:key="post-{{ $post->id }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($post->featured_image)
                                <img src="{{ \App\Services\ImageService::getThumbUrl($post->featured_image) }}" alt="{{ $post->title }}" class="w-12 h-8 object-cover rounded flex-shrink-0" loading="lazy">
                                @else
                                <div class="w-12 h-8 bg-gray-100 rounded flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="font-medium text-gray-800 hover:text-blue-700 line-clamp-1 block">{{ $post->title }}</a>
                                    <p class="text-xs text-gray-400">{{ $post->created_at->format('Y/m/d') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">{{ $post->type_ar }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 hidden md:table-cell text-xs">{{ $post->category?->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded-full font-medium
                                {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : ($post->status === 'draft' ? 'bg-gray-100 text-gray-600' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ match($post->status) { 'published' => 'منشور', 'draft' => 'مسودة', 'pending' => 'معلق', 'scheduled' => 'مجدول', default => $post->status } }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 hidden lg:table-cell">{{ number_format($post->views) }}</td>
                        <td class="px-4 py-3 text-gray-500 hidden lg:table-cell">{{ number_format($post->downloads) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.posts.edit', $post) }}" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50 transition">تعديل</a>
                                @if($post->status !== 'published')
                                <button wire:click="publish({{ $post->id }})" wire:confirm="نشر المقال الآن؟"
                                    class="text-green-600 hover:text-green-800 text-xs px-2 py-1 rounded hover:bg-green-50 transition">نشر</button>
                                @endif
                                <a href="{{ url($post->slug) }}" target="_blank" class="text-gray-400 hover:text-gray-600 text-xs px-2 py-1 rounded hover:bg-gray-50 transition">عرض</a>
                                <button wire:click="delete({{ $post->id }})" wire:confirm="هل أنت متأكد من حذف هذا المقال؟"
                                    class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50 transition">حذف</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            لا توجد مقالات
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $posts->links() }}
        </div>
    </div>
</div>
