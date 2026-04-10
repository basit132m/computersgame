@extends('layouts.app')

@section('title', 'تحميل: ' . $post->title)
@section('robots', 'noindex, nofollow')

@section('content')
<div class="max-w-2xl mx-auto">
    @adslot('timer_ad_top')

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden" x-data="downloadTimer()">
        {{-- Post Info --}}
        <div class="p-6 border-b text-center">
            @if($post->featured_image)
            <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}"
                class="w-32 h-20 object-cover rounded-xl mx-auto mb-4"
                width="128" height="80">
            @else
            <div class="w-32 h-20 bg-blue-100 rounded-xl mx-auto mb-4 flex items-center justify-center text-4xl">🎮</div>
            @endif
            <h1 class="text-xl font-bold text-gray-900">{{ $post->title }}</h1>
            @if($post->version)
            <p class="text-gray-500 text-sm mt-1">الإصدار: {{ $post->version }}</p>
            @endif
        </div>

        {{-- Timer --}}
        <div class="p-8 text-center" x-show="!ready">
            <div class="w-24 h-24 bg-blue-100 rounded-full mx-auto flex items-center justify-center mb-4">
                <span class="text-4xl font-bold text-blue-700" x-text="countdown"></span>
            </div>
            <p class="text-gray-600 text-lg font-medium">جاري تحضير رابط التحميل...</p>
            <p class="text-gray-400 text-sm mt-2">سيبدأ التحميل خلال <span x-text="countdown"></span> ثوانٍ</p>
            <div class="mt-4 w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-700 h-2 rounded-full transition-all duration-1000" :style="`width: ${(10 - countdown) * 10}%`"></div>
            </div>
        </div>

        {{-- Download Links (shown after timer) --}}
        <div class="p-6" x-show="ready" x-transition>
            <h2 class="text-lg font-bold text-gray-900 mb-4 text-center">✅ اختر رابط التحميل</h2>
            <div class="space-y-3">
                @forelse($post->downloadLinks as $link)
                <a href="{{ $link->url }}" target="_blank" rel="nofollow noopener"
                    onclick="trackLinkClick({{ $post->id }}, {{ $link->id }})"
                    class="flex items-center justify-between bg-blue-700 hover:bg-blue-800 text-white font-bold py-4 px-6 rounded-xl transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        {{ $link->label }}
                    </span>
                    <span class="text-sm text-blue-200">
                        @if($link->file_size){{ $link->file_size }}@endif
                        @if($link->version) - v{{ $link->version }}@endif
                    </span>
                </a>
                @empty
                <p class="text-center text-gray-500">لا توجد روابط تحميل متاحة حالياً</p>
                @endforelse
            </div>
            <p class="text-center text-xs text-gray-400 mt-4">تذكر: المحتوى مجاني للاستخدام الشخصي فقط</p>
        </div>

        {{-- Back Link --}}
        <div class="px-6 pb-6 text-center">
            <a href="{{ url($post->slug) }}" class="text-blue-700 hover:underline text-sm">
                ← العودة إلى صفحة {{ $post->title }}
            </a>
        </div>
    </div>

    @adslot('timer_ad_bottom')
</div>
@endsection

@section('scripts')
<script>
function downloadTimer() {
    return {
        countdown: 10,
        ready: false,
        init() {
            const timer = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) {
                    clearInterval(timer);
                    this.ready = true;
                }
            }, 1000);
        }
    }
}

function trackLinkClick(postId, linkId) {
    fetch('/download/click', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ post_id: postId, link_id: linkId, source: 'timer_page' })
    });
}
</script>
@endsection
