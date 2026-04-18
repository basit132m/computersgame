<div x-data="{
        copied: false,
        selectMode: new URLSearchParams(window.location.search).get('select') === '1',
        targetField: new URLSearchParams(window.location.search).get('field') ?? '',
        pickImage(path, url) {
            if (this.selectMode && window.opener) {
                window.opener.postMessage({ mediaField: this.targetField, path: path, url: url }, '*');
                window.close();
            }
        }
    }"
    @copy-url.window="navigator.clipboard.writeText($event.detail.url); copied = true; setTimeout(() => copied = false, 2000)">

    {{-- Select mode banner --}}
    <div x-show="selectMode" class="bg-blue-600 text-white px-5 py-3 rounded-xl mb-5 flex items-center gap-3">
        <i class="fas fa-hand-pointer text-lg"></i>
        <div>
            <p class="font-bold text-sm">وضع الاختيار — Select Mode</p>
            <p class="text-xs text-blue-100">انقر على صورة لإدراجها في المقال — Click an image to insert it into the post</p>
        </div>
        <button onclick="window.close()" class="mr-auto text-xs bg-white/20 hover:bg-white/30 px-3 py-1 rounded-lg transition">إلغاء</button>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>
    @endif

    {{-- Upload Zone --}}
    <div class="bg-white rounded-xl p-6 shadow-sm mb-6" x-show="!selectMode">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            رفع صور جديدة
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-600 mb-1">اختر الصور</label>
                <input type="file" wire:model="uploads" multiple accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">

                <div wire:loading wire:target="uploads" class="mt-3">
                    <div class="flex items-center gap-2 text-sm text-blue-600">
                        <div class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                        جاري معالجة الصور...
                    </div>
                </div>

                @error('uploads.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">النص البديل (alt)</label>
                <input type="text" wire:model="altText" placeholder="وصف الصورة بالعربية"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        @if(count($uploads))
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($uploads as $upload)
            <img src="{{ $upload->temporaryUrl() }}" class="w-20 h-14 object-cover rounded-lg border" loading="lazy">
            @endforeach
        </div>
        @endif

        <button wire:click="uploadFiles" wire:loading.attr="disabled"
            @class(['bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-6 rounded-lg transition', 'opacity-50 cursor-not-allowed' => count($uploads) === 0])
            @if(count($uploads) === 0) disabled @endif>
            <span wire:loading.remove wire:target="uploadFiles">رفع الصور</span>
            <span wire:loading wire:target="uploadFiles" class="flex items-center gap-2">
                <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                جاري الرفع...
            </span>
        </button>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-xl px-4 py-3 shadow-sm mb-5 flex items-center gap-3">
        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="بحث باسم الملف أو النص البديل..."
            class="flex-1 text-sm border-0 outline-none focus:ring-0 bg-transparent">
        @if($search)
        <button wire:click="$set('search', '')" class="text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        @endif
    </div>

    {{-- Copied notification --}}
    <div x-show="copied" x-transition class="fixed bottom-4 left-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg text-sm z-50">
        ✅ تم نسخ الرابط
    </div>

    {{-- Media Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        @forelse($mediaItems as $media)
        @php $imgUrl = asset('storage/' . $media->path); @endphp
        <div class="bg-white rounded-xl overflow-hidden shadow-sm group cursor-pointer"
             wire:key="media-{{ $media->id }}"
             x-on:click="selectMode && pickImage('{{ $media->path }}', '{{ $imgUrl }}')">
            <div class="relative aspect-video bg-gray-100">
                <img src="{{ $imgUrl }}" alt="{{ $media->alt_text }}"
                    class="w-full h-full object-cover transition"
                    :class="selectMode ? 'group-hover:scale-105 group-hover:opacity-90' : 'group-hover:opacity-80'"
                    loading="lazy">

                {{-- Normal mode overlay --}}
                <div class="absolute inset-0 flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition bg-black/30"
                     x-show="!selectMode">
                    <button wire:click.stop="copyUrl('{{ $media->path }}')"
                        class="bg-white text-gray-700 text-xs px-2 py-1 rounded hover:bg-blue-600 hover:text-white transition" title="نسخ الرابط">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                    <button wire:click.stop="delete({{ $media->id }})" wire:confirm="حذف هذه الصورة نهائياً؟"
                        class="bg-white text-red-600 text-xs px-2 py-1 rounded hover:bg-red-600 hover:text-white transition" title="حذف">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>

                {{-- Select mode overlay --}}
                <div class="absolute inset-0 flex items-end justify-center pb-2 opacity-0 group-hover:opacity-100 transition"
                     x-show="selectMode">
                    <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-lg shadow">اختر هذه الصورة</span>
                </div>
            </div>
            <div class="p-2" x-show="!selectMode">
                <input type="text" value="{{ $media->alt_text }}"
                    wire:change="updateAlt({{ $media->id }}, $event.target.value)"
                    class="w-full text-xs border-0 focus:ring-1 focus:ring-blue-500 rounded px-1 py-0.5 text-gray-600"
                    placeholder="النص البديل...">
                <p class="text-xs text-gray-400 mt-1 truncate" title="{{ $media->original_name }}">{{ $media->original_name }}</p>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            {{ $search ? 'لا توجد نتائج للبحث' : 'لم يتم رفع أي صور بعد' }}
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $mediaItems->links() }}
    </div>
</div>
