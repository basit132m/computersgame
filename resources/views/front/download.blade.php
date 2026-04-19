@extends('layouts.app')

@section('title', 'تحميل: ' . $post->title)
@section('robots', 'noindex, nofollow')

@section('content')
<div class="flex gap-5">

    {{-- ═══ Main Download Area ═══ --}}
    <div class="flex-1 min-w-0">

        @adslot('timer_ad_top')

        {{-- Hero Banner --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-4">
            {{-- Top gradient bar --}}
            <div class="h-1.5" style="background:linear-gradient(90deg,#30A38A,#2563eb,#7c3aed);"></div>

            <div class="p-6">
                <div class="flex gap-5 items-start">
                    {{-- Thumbnail --}}
                    <div class="flex-shrink-0">
                        @if($post->featured_image)
                        <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}"
                             class="w-28 h-28 object-cover rounded-xl border border-gray-200 shadow-sm"
                             width="112" height="112">
                        @else
                        <div class="w-28 h-28 rounded-xl flex items-center justify-center text-5xl"
                             style="background:linear-gradient(135deg,#e0f2fe,#dbeafe);">🎮</div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl font-bold text-gray-900 leading-snug mb-2">{{ $post->title }}</h1>
                        <div class="flex flex-wrap gap-2 text-xs mb-3">
                            @if($post->version)
                            <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded-full font-medium">
                                <i class="fas fa-code-branch ml-1"></i>v{{ $post->version }}
                            </span>
                            @endif
                            @if($post->file_size)
                            <span class="bg-green-50 text-green-700 px-2 py-1 rounded-full font-medium">
                                <i class="fas fa-hdd ml-1"></i>{{ $post->file_size }}
                            </span>
                            @endif
                            @if($post->platform_ar)
                            <span class="bg-purple-50 text-purple-700 px-2 py-1 rounded-full font-medium">
                                <i class="fas fa-desktop ml-1"></i>{{ $post->platform_ar }}
                            </span>
                            @endif
                            @if($post->category)
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full font-medium">
                                {{ $post->category->name }}
                            </span>
                            @endif
                        </div>
                        <a href="{{ url($post->slug) }}" class="text-xs text-[#30A38A] hover:underline flex items-center gap-1">
                            <i class="fas fa-arrow-right"></i> العودة إلى صفحة المقال
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Timer + Download Card --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-4" x-data="downloadTimer()">

            {{-- Timer Section --}}
            <div x-show="!ready" class="p-8 text-center">
                <p class="text-sm font-medium text-gray-500 mb-5 tracking-wide uppercase">جاري تحضير رابط التحميل</p>

                {{-- Circular countdown --}}
                <div class="relative w-32 h-32 mx-auto mb-6">
                    <svg class="w-32 h-32 -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="54" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                        <circle cx="60" cy="60" r="54" fill="none" stroke="#2563eb" stroke-width="8"
                                stroke-linecap="round" stroke-dasharray="339.3"
                                :stroke-dashoffset="339.3 - (339.3 * (10 - countdown) / 10)"
                                style="transition:stroke-dashoffset 1s linear;"></circle>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-black text-blue-700" x-text="countdown"></span>
                        <span class="text-xs text-gray-400">ثانية</span>
                    </div>
                </div>

                <p class="text-gray-600 font-medium mb-1">يرجى الانتظار...</p>
                <p class="text-gray-400 text-sm">سيظهر رابط التحميل بعد <span class="font-bold text-blue-700" x-text="countdown"></span> ثوانٍ</p>

                {{-- Security badges --}}
                <div class="flex justify-center gap-4 mt-6 text-xs text-gray-400">
                    <span><i class="fas fa-shield-alt text-green-500 ml-1"></i>آمن 100%</span>
                    <span><i class="fas fa-bolt text-yellow-500 ml-1"></i>تحميل سريع</span>
                    <span><i class="fas fa-gift text-red-500 ml-1"></i>مجاني تماماً</span>
                </div>
            </div>

            {{-- Download Links (shown after timer) --}}
            @php $timerDirectUrl = \App\Models\AdSlot::where('slot_key','timer_page_direct_url')->where('active',true)->value('code'); @endphp
            <div x-show="ready" x-transition class="p-6">
                {{-- Success header --}}
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">الرابط جاهز للتحميل!</h2>
                    <p class="text-sm text-gray-500 mt-1">اختر رابط التحميل المناسب لك</p>
                </div>

                <div class="space-y-3 max-w-md mx-auto">
                    @forelse($post->downloadLinks as $link)
                    <button type="button"
                        onclick="handleTimerDownload(this, {{ $post->id }}, {{ $link->id }}, '{{ addslashes($link->url) }}', '{{ addslashes($timerDirectUrl ?? '') }}')"
                        class="flex items-center justify-between w-full text-white font-bold py-4 px-5 rounded-xl transition hover:opacity-90 hover:shadow-lg"
                        style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">
                        <span class="flex items-center gap-3">
                            <span class="w-9 h-9 bg-white bg-opacity-20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-download text-base"></i>
                            </span>
                            <span>
                                <span class="block btn-label-{{ $link->id }}">{{ $link->label }}</span>
                                @if($link->version)
                                <span class="text-blue-200 text-xs font-normal">v{{ $link->version }}</span>
                                @endif
                            </span>
                        </span>
                        @if($link->file_size)
                        <span class="text-sm text-blue-200 flex-shrink-0">{{ $link->file_size }}</span>
                        @endif
                    </button>
                    @empty
                    <p class="text-center text-gray-500 py-4">لا توجد روابط تحميل متاحة حالياً</p>
                    @endforelse
                </div>

                <p class="text-center text-xs text-gray-400 mt-5">
                    <i class="fas fa-info-circle ml-1"></i>المحتوى مجاني للاستخدام الشخصي فقط
                </p>
            </div>
        </div>

        @adslot('timer_ad_bottom')

    </div>

    {{-- ═══ Sidebar ═══ --}}
    <div class="hidden lg:block w-80 flex-shrink-0" style="position:sticky;bottom:1rem;align-self:flex-start;">
        <x-front.sidebar :trending="$sidebarTrending" :tags="$sidebarTags" />
    </div>

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
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ post_id: postId, link_id: linkId, source: 'timer_page' })
    });
}

function handleTimerDownload(btn, postId, linkId, downloadUrl, directUrl) {
    trackLinkClick(postId, linkId);

    if (!directUrl) {
        window.open(downloadUrl, '_blank', 'noopener');
        return;
    }

    const key = 'timer_click_' + linkId;
    const clicked = sessionStorage.getItem(key);

    if (!clicked) {
        sessionStorage.setItem(key, '1');
        window.open(directUrl, '_blank', 'noopener');
        const label = btn.querySelector('.btn-label-' + linkId);
        if (label) label.textContent = 'اضغط مرة أخرى للتحميل';
    } else {
        sessionStorage.removeItem(key);
        window.open(downloadUrl, '_blank', 'noopener');
    }
}
</script>
@endsection
