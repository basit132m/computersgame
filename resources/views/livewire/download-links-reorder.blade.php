<div>
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-lg mb-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Add new link form --}}
    <div class="bg-gray-50 rounded-xl p-4 mb-4 border">
        <h4 class="font-semibold text-gray-700 mb-3 text-sm">إضافة رابط تحميل جديد</h4>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-3">
            <div>
                <input wire:model="newLabel" type="text" placeholder="العنوان (مثل: جوجل درايف)*"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('newLabel') border-red-500 @enderror">
                @error('newLabel')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <input wire:model="newUrl" type="url" placeholder="رابط التحميل*"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('newUrl') border-red-500 @enderror">
                @error('newUrl')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <input wire:model="newFileSize" type="text" placeholder="حجم الملف (مثل: 2.5 GB)"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <input wire:model="newVersion" type="text" placeholder="الإصدار"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <input wire:model="newPlatform" type="text" placeholder="المنصة (PC / Android)"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <button wire:click="addLink" class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-bold py-2 px-6 rounded-lg transition">
            + إضافة الرابط
        </button>
    </div>

    {{-- Sortable Links List --}}
    @if(count($links))
    <div
        x-data="{
            links: @js(collect($links)->pluck('id')->values()),
            init() {
                new Sortable(this.$el, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    handle: '.drag-handle',
                    onEnd: () => {
                        const order = [...this.$el.querySelectorAll('[data-id]')].map(el => parseInt(el.dataset.id));
                        $wire.dispatch('reorder', { order });
                    }
                });
            }
        }"
        class="space-y-2"
    >
        @foreach($links as $link)
        <div data-id="{{ $link['id'] }}" wire:key="link-{{ $link['id'] }}"
            class="bg-white border rounded-xl p-3 flex items-center gap-3 group">
            {{-- Drag Handle --}}
            <div class="drag-handle cursor-grab text-gray-300 hover:text-gray-500 flex-shrink-0">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-4.001A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z"/>
                </svg>
            </div>

            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-gray-800">{{ $link['label'] }}</p>
                <p class="text-xs text-gray-500 truncate">{{ $link['url'] }}</p>
                <div class="flex gap-3 text-xs text-gray-400 mt-0.5">
                    @if($link['file_size'])<span>{{ $link['file_size'] }}</span>@endif
                    @if($link['version'])<span>v{{ $link['version'] }}</span>@endif
                    @if($link['platform'])<span>{{ $link['platform'] }}</span>@endif
                    <span>{{ $link['clicks'] }} نقرة</span>
                </div>
            </div>

            <button wire:click="delete({{ $link['id'] }})" wire:confirm="حذف هذا الرابط؟"
                class="text-red-400 hover:text-red-600 flex-shrink-0 opacity-0 group-hover:opacity-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
        @endforeach
    </div>

    <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        اسحب لإعادة الترتيب
    </p>
    @else
    <p class="text-gray-400 text-sm text-center py-6">لا توجد روابط تحميل بعد. أضف رابطاً أعلاه.</p>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush
