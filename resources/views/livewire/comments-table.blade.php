<div>
    {{-- Toast --}}
    @if($toast)
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-300 text-green-800 rounded-lg flex items-center justify-between text-sm font-medium"
         wire:poll.2s="clearToast">
        <span>{{ $toast }}</span>
        <button wire:click="clearToast" class="text-green-600 hover:text-green-800 text-lg leading-none">&times;</button>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl p-4 mb-4 flex flex-wrap gap-3 shadow-sm items-center">
        <div class="flex items-center gap-2 flex-1 min-w-48 border rounded-lg px-3 py-2">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="بحث في التعليقات..." class="flex-1 border-0 focus:ring-0 text-sm outline-none">
            <div wire:loading wire:target="search" class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        </div>

        <select wire:model.live="status" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">جميع الحالات</option>
            <option value="pending">معلق</option>
            <option value="approved">موافق عليه</option>
            <option value="rejected">مرفوض</option>
        </select>

        {{-- Bulk actions --}}
        @if(count($selected) > 0)
        <div class="flex items-center gap-2 border-r pr-3">
            <span class="text-sm text-gray-600">{{ count($selected) }} محدد</span>
            <button wire:click="bulkApprove" class="text-xs bg-green-100 text-green-700 px-3 py-1.5 rounded-lg hover:bg-green-200 transition">موافقة</button>
            <button wire:click="bulkReject" class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1.5 rounded-lg hover:bg-yellow-200 transition">رفض</button>
            <button wire:click="bulkDelete" wire:confirm="حذف التعليقات المحددة؟" class="text-xs bg-red-100 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-200 transition">حذف</button>
        </div>
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 w-8">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300">
                    </th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">المعلق</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">التعليق</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden lg:table-cell">المقال</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">الحالة</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($comments as $comment)
                <tr class="hover:bg-gray-50" wire:key="comment-{{ $comment->id }}">
                    <td class="px-4 py-3">
                        <input type="checkbox" wire:model.live="selected" value="{{ $comment->id }}" class="rounded border-gray-300">
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-800 text-sm">{{ $comment->name }}</p>
                        <p class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                        @if($comment->email)
                        <p class="text-xs text-gray-400">{{ $comment->email }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell max-w-xs">
                        <p class="text-sm text-gray-700 line-clamp-2">{{ $comment->body }}</p>
                    </td>
                    <td class="px-4 py-3 hidden lg:table-cell">
                        @if($comment->post)
                        <a href="{{ route('admin.posts.edit', $comment->post_id) }}" class="text-xs text-blue-600 hover:underline line-clamp-1">{{ $comment->post->title }}</a>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-1 rounded-full font-medium
                            {{ $comment->status === 'approved' ? 'bg-green-100 text-green-700' : ($comment->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ match($comment->status) { 'approved' => 'موافق', 'rejected' => 'مرفوض', default => 'معلق' } }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1">
                            @if($comment->status !== 'approved')
                            <button wire:click="approve({{ $comment->id }})"
                                class="text-green-600 hover:text-green-800 text-xs px-2 py-1 rounded hover:bg-green-50 transition flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                موافقة
                            </button>
                            @endif
                            @if($comment->status !== 'rejected')
                            <button wire:click="reject({{ $comment->id }})"
                                class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50 transition">رفض</button>
                            @endif
                            <button wire:click="removeComment({{ $comment->id }})" wire:confirm="حذف هذا التعليق؟"
                                class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50 transition">حذف</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400">لا توجد تعليقات</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">
            {{ $comments->links() }}
        </div>
    </div>
</div>
