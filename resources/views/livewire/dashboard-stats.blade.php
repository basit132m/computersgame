<div>
    {{-- Auto-refreshes every 60 seconds via #[Polling('60s')] --}}
    <div class="flex items-center gap-2 mb-4 text-xs text-gray-400">
        <div wire:loading class="w-3 h-3 border-2 border-blue-400 border-t-transparent rounded-full animate-spin"></div>
        <span wire:loading>جاري التحديث...</span>
        <span wire:loading.remove>يتجدد تلقائياً كل 60 ثانية</span>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-sm border-r-4 border-blue-500">
            <p class="text-xs text-gray-500 mb-1">إجمالي المقالات</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_posts']) }}</p>
            <p class="text-xs text-blue-600 mt-1">{{ $stats['published_posts'] }} منشور</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-r-4 border-green-500">
            <p class="text-xs text-gray-500 mb-1">تحميلات اليوم</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_downloads_today']) }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-r-4 border-indigo-500">
            <p class="text-xs text-gray-500 mb-1">تحميلات هذا الأسبوع</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_downloads_week']) }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-r-4 border-yellow-500">
            <p class="text-xs text-gray-500 mb-1">تعليقات معلقة</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending_comments']) }}</p>
            @if($stats['pending_comments'] > 0)
            <a href="{{ route('admin.comments.index') }}" class="text-xs text-yellow-600 hover:underline">مراجعة</a>
            @endif
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-r-4 border-purple-500">
            <p class="text-xs text-gray-500 mb-1">إجراءات سريعة</p>
            <a href="{{ route('admin.posts.create') }}" class="block text-xs bg-blue-700 text-white px-3 py-1.5 rounded hover:bg-blue-800 transition text-center mt-2">+ مقال جديد</a>
        </div>
    </div>

    {{-- Top Downloaded This Week --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/></svg>
            الأكثر تحميلاً هذا الأسبوع
        </h2>
        @if($topDownloaded->count())
        <ol class="space-y-3">
            @foreach($topDownloaded as $i => $post)
            <li class="flex items-center gap-3">
                <span class="w-7 h-7 bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0">{{ $i + 1 }}</span>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('admin.posts.edit', $post) }}" class="font-medium text-gray-800 hover:text-blue-700 text-sm truncate block">{{ $post->title }}</a>
                    <p class="text-xs text-gray-400">{{ number_format($post->weekly_downloads) }} تحميل</p>
                </div>
            </li>
            @endforeach
        </ol>
        @else
        <p class="text-gray-400 text-sm">لا توجد بيانات تحميل بعد</p>
        @endif
    </div>
</div>
