<div>
    <div class="bg-white rounded-xl p-6 shadow-sm mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-gray-800">فاحص الروابط المعطوبة</h3>
                <p class="text-sm text-gray-500">يفحص جميع روابط التحميل ويكتشف الروابط المعطوبة</p>
            </div>
            <button wire:click="startCheck" wire:loading.attr="disabled"
                @class(['bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-6 rounded-lg transition', 'opacity-50 cursor-not-allowed' => $running])>
                <span wire:loading.remove wire:target="startCheck">
                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    بدء الفحص
                </span>
                <span wire:loading wire:target="startCheck" class="flex items-center gap-2">
                    <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    جاري الفحص...
                </span>
            </button>
        </div>

        {{-- Progress --}}
        @if($running || $checked > 0)
        <div class="mb-4">
            <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span>تم فحص {{ $checked }} من {{ $total }} رابط</span>
                @if(!$running && count($broken) > 0)
                <span class="text-red-600 font-medium">{{ count($broken) }} رابط معطوب</span>
                @elseif(!$running && $checked > 0)
                <span class="text-green-600 font-medium">✅ جميع الروابط سليمة</span>
                @endif
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                    style="width: {{ $total > 0 ? ($checked / $total * 100) : 0 }}%"></div>
            </div>
        </div>
        @endif

        {{-- Results summary --}}
        @if(count($results) > 0 && !$running)
        <div class="flex gap-4 p-3 bg-gray-50 rounded-lg text-sm">
            <span class="text-green-600">✅ {{ count(array_filter($results, fn($r) => $r['ok'])) }} سليم</span>
            <span class="text-red-600">❌ {{ count($broken) }} معطوب</span>
        </div>
        @endif
    </div>

    {{-- Results Table --}}
    @if(count($results) > 0)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">الرابط</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">المقال</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">كود HTTP</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($results as $result)
                    @if(!$result['ok'])
                    <tr class="bg-red-50" wire:key="result-{{ $result['id'] }}">
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 text-red-600 text-xs font-medium">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                معطوب
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $result['label'] }}</p>
                            <a href="{{ $result['url'] }}" target="_blank" rel="noopener" class="text-xs text-gray-500 hover:text-blue-600 truncate block max-w-xs">{{ $result['url'] }}</a>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            @if($result['slug'])
                            <a href="{{ route('admin.posts.edit', ['post' => $result['slug']]) }}" class="text-blue-600 hover:underline text-xs">{{ $result['post'] }}</a>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-red-600 font-mono text-xs">{{ $result['status'] ?: 'خطأ' }}</span>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                    @if(count($broken) === 0)
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-green-600 font-medium">
                            ✅ جميع الروابط تعمل بشكل صحيح
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
