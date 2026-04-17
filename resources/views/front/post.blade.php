@extends('layouts.app')

@section('title', $post->meta_title ?: $post->title . ' - تحميل مجاني')
@section('meta_description', $post->meta_description ?: strip_tags($post->excerpt))
@section('meta_keywords', $post->meta_keywords)
@section('robots', $post->robots)
@section('canonical', $post->canonical_url ?: url($post->slug))
@section('og_type', 'article')
@section('og_title', $post->og_title ?: $post->title)
@section('og_description', $post->og_description ?: strip_tags($post->excerpt))
@if($post->og_image)@section('og_image', asset('storage/'.$post->og_image))@endif

@section('schema')
<script type="application/ld+json">
{!! json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {"@type": "ListItem", "position": 1, "name": "الرئيسية", "item": "{{ url('/') }}"},
        @if($post->category)
        {"@type": "ListItem", "position": 2, "name": "{{ $post->category->name }}", "item": "{{ route('category.show', $post->category->slug) }}"},
        {"@type": "ListItem", "position": 3, "name": "{{ $post->title }}", "item": "{{ url($post->slug) }}"}
        @else
        {"@type": "ListItem", "position": 2, "name": "{{ $post->title }}", "item": "{{ url($post->slug) }}"}
        @endif
    ]
}
</script>
@endsection

@php
    $kw = $post->focus_keyword ?: $post->title;

    // Auto-insert featured image after first paragraph
    $content = $post->content ?? '';
    if ($post->featured_image && $content) {
        $pos = mb_strpos($content, '</p>');
        if ($pos !== false) {
            $imgTag = '<figure style="margin:1.25rem 0;text-align:center"><img src="' . asset('storage/' . $post->featured_image) . '" alt="' . e($post->title) . '" style="max-width:100%;border-radius:8px;" loading="lazy"></figure>';
            $content = mb_substr($content, 0, $pos + 4) . $imgTag . mb_substr($content, $pos + 4);
        }
    }
@endphp

@section('content')
<div class="flex gap-5">

    {{-- ═══ Main Article ═══ --}}
    <article class="flex-1 min-w-0">

        {{-- Breadcrumb --}}
        <nav class="mb-3 text-xs text-gray-500 flex flex-wrap items-center gap-1">
            <a href="{{ url('/') }}" class="hover:text-[#30A38A] transition"><i class="fas fa-home text-[10px]"></i> الرئيسية</a>
            @if($post->category)
            <i class="fas fa-angle-left text-[10px] text-gray-400"></i>
            <a href="{{ route('category.show', $post->category->slug) }}" class="hover:text-[#30A38A] transition">{{ $post->category->name }}</a>
            @endif
            <i class="fas fa-angle-left text-[10px] text-gray-400"></i>
            <span class="text-gray-700 truncate max-w-xs">{{ $post->title }}</span>
        </nav>

        {{-- Title card --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-4 px-5 py-4">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 leading-snug mb-2">
                <i class="fas fa-download text-[#30A38A] text-base ml-1"></i>
                {{ $post->title }}
            </h1>
            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500">
                @if($post->published_at)
                <span class="flex items-center gap-1"><i class="fas fa-calendar-alt text-[#30A38A]"></i> {{ $post->published_at->format('Y/m/d') }}</span>
                @endif
                @if($post->category)
                <a href="{{ route('category.show', $post->category->slug) }}" class="flex items-center gap-1 hover:text-[#30A38A] transition">
                    <i class="fas fa-folder text-[#30A38A]"></i> {{ $post->category->name }}
                </a>
                @endif
                <span class="flex items-center gap-1"><i class="fas fa-eye text-[#30A38A]"></i> {{ number_format($post->views) }} مشاهدة</span>
            </div>
        </div>

        {{-- Banner image --}}
        @if($post->banner_image)
        <div class="mb-4 rounded-xl overflow-hidden border border-gray-200">
            <img src="{{ asset('storage/'.$post->banner_image) }}" alt="{{ $post->title }}" class="w-full object-cover" loading="eager">
        </div>
        @endif

        {{-- Header Ad --}}
        @adslot('header_ad')

        {{-- ═══════════════════════════════════════════════════════
             UNIFIED ARTICLE CARD — all sections in one div
        ═══════════════════════════════════════════════════════ --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-4">

            {{-- ① معلومات حول --}}
            @php
                $hasSpecs = $post->version || $post->developer || $post->publisher || $post->file_size
                         || $post->updated_date || $post->game_language
                         || $post->category || $post->platform || $post->published_at;
            @endphp
            @if($hasSpecs)
            <div class="p-5 border-b border-gray-100">
                <h2 class="section-title">معلومات حول تحميل {{ $kw }}</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    {{-- اسم اللعبة — highlighted --}}
                    <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                        <div class="flex flex-col items-center justify-center py-4 px-2" style="background:#4a6b7a;">
                            <i class="fas fa-gamepad text-white text-2xl mb-1"></i>
                            <p class="text-white text-xs">اسم اللعبة</p>
                        </div>
                        <div class="bg-white text-center py-2 px-2">
                            <p class="text-sm font-bold text-gray-800 leading-snug">{{ Str::limit($post->title, 28) }}</p>
                        </div>
                    </div>
                    @if($post->developer)
                    <div class="bg-gray-100 rounded-xl text-center py-4 px-2 flex flex-col items-center justify-between">
                        <i class="fas fa-building text-2xl mb-1" style="color:#4a6b7a;"></i>
                        <p class="text-xs mb-1" style="color:#4a6b7a;">الشركة المنتجة</p>
                        <div class="w-8 h-px bg-gray-300 mb-1"></div>
                        <p class="text-sm font-bold text-gray-800">{{ $post->developer }}</p>
                    </div>
                    @endif
                    @if($post->publisher)
                    <div class="bg-gray-100 rounded-xl text-center py-4 px-2 flex flex-col items-center justify-between">
                        <i class="fas fa-briefcase text-2xl mb-1" style="color:#4a6b7a;"></i>
                        <p class="text-xs mb-1" style="color:#4a6b7a;">الناشر</p>
                        <div class="w-8 h-px bg-gray-300 mb-1"></div>
                        <p class="text-sm font-bold text-gray-800">{{ $post->publisher }}</p>
                    </div>
                    @endif
                    @if($post->published_at)
                    <div class="bg-gray-100 rounded-xl text-center py-4 px-2 flex flex-col items-center justify-between">
                        <i class="fas fa-calendar-check text-2xl mb-1" style="color:#4a6b7a;"></i>
                        <p class="text-xs mb-1" style="color:#4a6b7a;">تاريخ النشر</p>
                        <div class="w-8 h-px bg-gray-300 mb-1"></div>
                        <p class="text-sm font-bold text-gray-800">{{ $post->published_at->translatedFormat('j F Y') }}</p>
                    </div>
                    @endif
                    <div class="bg-gray-100 rounded-xl text-center py-4 px-2 flex flex-col items-center justify-between">
                        <i class="fas fa-expand-arrows-alt text-2xl mb-1" style="color:#4a6b7a;"></i>
                        <p class="text-xs mb-1" style="color:#4a6b7a;">متوافق مع</p>
                        <div class="w-8 h-px bg-gray-300 mb-1"></div>
                        <p class="text-sm font-bold text-gray-800">{{ $post->platform_ar }}</p>
                    </div>
                    @if($post->file_size)
                    <div class="bg-gray-100 rounded-xl text-center py-4 px-2 flex flex-col items-center justify-between">
                        <i class="fas fa-download text-2xl mb-1" style="color:#4a6b7a;"></i>
                        <p class="text-xs mb-1" style="color:#4a6b7a;">حجم اللعبة</p>
                        <div class="w-8 h-px bg-gray-300 mb-1"></div>
                        <p class="text-sm font-bold text-gray-800">{{ $post->file_size }}</p>
                    </div>
                    @endif
                    @if($post->version)
                    <div class="bg-gray-100 rounded-xl text-center py-4 px-2 flex flex-col items-center justify-between">
                        <i class="fas fa-code-branch text-2xl mb-1" style="color:#4a6b7a;"></i>
                        <p class="text-xs mb-1" style="color:#4a6b7a;">نسخة اللعبة</p>
                        <div class="w-8 h-px bg-gray-300 mb-1"></div>
                        <p class="text-sm font-bold text-gray-800">{{ $post->version }}</p>
                    </div>
                    @endif
                    @if($post->updated_date)
                    <div class="bg-gray-100 rounded-xl text-center py-4 px-2 flex flex-col items-center justify-between">
                        <i class="fas fa-clock text-2xl mb-1" style="color:#4a6b7a;"></i>
                        <p class="text-xs mb-1" style="color:#4a6b7a;">تاريخ التحديث</p>
                        <div class="w-8 h-px bg-gray-300 mb-1"></div>
                        <p class="text-sm font-bold text-gray-800">{{ $post->updated_date->translatedFormat('j F Y') }}</p>
                    </div>
                    @endif
                    @if($post->game_language)
                    <div class="bg-gray-100 rounded-xl text-center py-4 px-2 flex flex-col items-center justify-between">
                        <i class="fas fa-language text-2xl mb-1" style="color:#4a6b7a;"></i>
                        <p class="text-xs mb-1" style="color:#4a6b7a;">لغة اللعبة</p>
                        <div class="w-8 h-px bg-gray-300 mb-1"></div>
                        <p class="text-sm font-bold text-gray-800">{{ $post->game_language }}</p>
                    </div>
                    @endif
                    @if($post->category)
                    <div class="bg-gray-100 rounded-xl text-center py-4 px-2 flex flex-col items-center justify-between">
                        <i class="fas fa-folder-open text-2xl mb-1" style="color:#4a6b7a;"></i>
                        <p class="text-xs mb-1" style="color:#4a6b7a;">التصنيف</p>
                        <div class="w-8 h-px bg-gray-300 mb-1"></div>
                        <p class="text-sm font-bold text-gray-800">{{ $post->category->name }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- ② Content --}}
            @if($content)
            <div class="p-5 border-b border-gray-100">
                <div class="prose prose-sm max-w-none">
                    {!! $content !!}
                </div>
            </div>
            @endif

            {{-- In-Content Ad --}}
            @adslot('in_content_ad')

            {{-- ③ مميزات --}}
            @if($post->features)
            <div class="p-5 border-b border-gray-100">
                <h2 class="section-title">مميزات {{ $kw }}</h2>
                @foreach(array_filter(explode("\n", $post->features)) as $line)
                <div class="flex items-start gap-2 mb-2">
                    <i class="fas fa-check text-[#30A38A] text-xs mt-1 flex-shrink-0"></i>
                    <span class="text-sm text-gray-700">{{ trim($line) }}</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- ④ المميزات والعيوب --}}
            @if($post->pros || $post->cons)
            <div class="p-5 border-b border-gray-100">
                <h2 class="section-title">المميزات والعيوب</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($post->pros)
                    <div>
                        <h3 class="font-bold text-green-600 mb-2 flex items-center gap-1 text-sm"><i class="fas fa-thumbs-up"></i> المميزات</h3>
                        <div class="text-sm text-gray-700 space-y-1">{!! nl2br(e($post->pros)) !!}</div>
                    </div>
                    @endif
                    @if($post->cons)
                    <div>
                        <h3 class="font-bold text-red-500 mb-2 flex items-center gap-1 text-sm"><i class="fas fa-thumbs-down"></i> العيوب</h3>
                        <div class="text-sm text-gray-700 space-y-1">{!! nl2br(e($post->cons)) !!}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- ⑤ ما الجديد --}}
            @if($post->whats_new)
            <div class="p-5 border-b border-gray-100">
                <h2 class="section-title">ما الجديد في هذا الإصدار</h2>
                <div class="text-sm text-gray-700 prose max-w-none">{!! nl2br(e($post->whats_new)) !!}</div>
            </div>
            @endif

            {{-- ⑥ فيديو شرح --}}
            @if($post->youtube_url)
            @php
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $post->youtube_url, $m);
                $ytId = $m[1] ?? null;
            @endphp
            @if($ytId)
            <div class="border-b border-gray-100">
                <div class="px-5 pt-5 pb-3">
                    <h2 class="section-title">فيديو شرح {{ $kw }}</h2>
                </div>
                <div class="aspect-video">
                    <iframe src="https://www.youtube.com/embed/{{ $ytId }}"
                            title="{{ $post->title }} - فيديو"
                            class="w-full h-full" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
            @endif
            @endif

            {{-- ⑦ صور من داخل --}}
            @if($post->gallery_images && count($post->gallery_images))
            <div class="p-5 border-b border-gray-100">
                <h2 class="section-title">صور من داخل {{ $kw }}</h2>
                <div class="flex flex-col gap-3">
                    @foreach($post->gallery_images as $img)
                    <a href="{{ asset('storage/'.$img) }}" target="_blank" class="block overflow-hidden rounded-lg">
                        <img src="{{ asset('storage/'.$img) }}" alt="{{ $post->title }}"
                             class="w-full object-cover hover:opacity-90 transition" loading="lazy">
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ⑧ متطلبات تشغيل --}}
            @php
                $hasSysReq = $post->sys_req_os || $post->sys_req_cpu || $post->sys_req_gpu
                          || $post->sys_req_ram || $post->sys_req_storage || $post->sys_req_software
                          || $post->system_requirements;
            @endphp
            @if($hasSysReq)
            <div class="p-5 border-b border-gray-100">
                <h2 class="section-title">متطلبات تشغيل {{ $kw }}</h2>
                @if($post->sys_req_os || $post->sys_req_cpu || $post->sys_req_gpu || $post->sys_req_ram || $post->sys_req_storage || $post->sys_req_software)
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="w-full text-sm">
                        <tbody>
                            @foreach([
                                ['نظام التشغيل','sys_req_os'],['المعالج','sys_req_cpu'],
                                ['كارت الشاشة','sys_req_gpu'],['الذاكرة RAM','sys_req_ram'],
                                ['مساحة التخزين','sys_req_storage'],['البرامج المطلوبة','sys_req_software'],
                            ] as [$label,$field])
                                @if($post->$field)
                                <tr class="border-b border-gray-100 last:border-0">
                                    <td class="py-2.5 px-4 font-medium w-2/5 text-white" style="background:#4a6b7a;">{{ $label }}</td>
                                    <td class="py-2.5 px-4 text-gray-700">{{ $post->$field }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div>
                    @foreach(array_filter(explode("\n", $post->system_requirements)) as $line)
                    <div class="flex items-start gap-2 mb-2">
                        <i class="fas fa-check text-[#30A38A] text-xs mt-1 flex-shrink-0"></i>
                        <span class="text-sm text-gray-700">{{ trim($line) }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            {{-- ⑨ تحميل --}}
            @if($post->downloadLinks->count())
            <div class="p-5 border-b border-gray-100">
                {{-- Title bar --}}
                <div class="flex items-center justify-end gap-3 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 mb-3">
                    <span class="font-bold text-gray-800 text-sm leading-snug">
                        تحميل لعبة {{ $post->title }} للكمبيوتر {{ $post->downloadLinks->count() > 1 ? 'بروابط مباشرة' : 'برابط واحد مباشر' }}
                    </span>
                    <i class="fas fa-cloud-download-alt text-gray-500 text-xl flex-shrink-0"></i>
                </div>
                {{-- Category line --}}
                @if($post->category)
                <p class="text-center text-xs text-gray-500 mb-3">من قسم : {{ $post->category->name }}</p>
                @endif
                {{-- Link buttons --}}
                <div class="flex flex-col gap-3">
                    @foreach($post->downloadLinks as $link)
                    <a href="{{ route('download.show', $post->slug) }}" target="_blank"
                       onclick="trackDownload({{ $post->id }})"
                       class="flex items-center rounded-lg overflow-hidden w-full hover:opacity-90 transition"
                       style="background:#2563eb;">
                        <span class="flex-1 flex items-center justify-center gap-2 py-4 px-4 text-white font-bold text-base">
                            <i class="fab fa-windows text-xl"></i>
                            {{ $link->label }}
                        </span>
                        @if($link->file_size)
                        <span class="py-4 px-5 text-white font-bold text-sm flex-shrink-0 border-r border-blue-400" style="background:#1e3a8a;">
                            {{ $link->file_size }}
                        </span>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ⑩ تقييم --}}
            <div class="p-5 border-b border-gray-100"
                 x-data="ratingWidget({{ $post->id }}, {{ $post->average_rating }}, {{ $post->ratings_count }}, {{ $userRating ?? 'null' }})">
                <h2 class="section-title">تقييم {{ $kw }}</h2>
                <div class="flex items-center gap-6">
                    <div class="text-center border-l pl-6">
                        <p class="text-4xl font-bold text-gray-800" x-text="avgRating.toFixed(1)"></p>
                        <p class="text-xs text-gray-500 mt-1"><span x-text="ratingCount"></span> تقييم</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-2">أضف تقييمك:</p>
                        <div class="flex gap-1">
                            <template x-for="star in 5" :key="star">
                                <button @click="rate(star)" class="text-2xl transition"
                                        :class="star <= (hover || userRating || avgRating) ? 'text-yellow-400' : 'text-gray-300'"
                                        @mouseenter="hover = star" @mouseleave="hover = 0">★</button>
                            </template>
                        </div>
                        <p x-show="message" x-text="message" class="text-green-600 text-xs mt-1"></p>
                    </div>
                </div>
            </div>

            {{-- ⑪ مشاركة المقال --}}
            <div class="p-5">
                <h2 class="section-title">مشاركة المقال</h2>
                <div class="flex flex-wrap gap-2">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url($post->slug)) }}"
                       target="_blank" rel="noopener nofollow"
                       class="flex items-center gap-2 bg-blue-600 text-white text-xs px-4 py-2 rounded hover:bg-blue-700 transition">
                        <i class="fab fa-facebook-f"></i> فيسبوك
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url($post->slug)) }}"
                       target="_blank" rel="noopener nofollow"
                       class="flex items-center gap-2 bg-sky-500 text-white text-xs px-4 py-2 rounded hover:bg-sky-600 transition">
                        <i class="fab fa-x-twitter"></i> تويتر
                    </a>
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . url($post->slug)) }}"
                       target="_blank" rel="noopener nofollow"
                       class="flex items-center gap-2 bg-green-500 text-white text-xs px-4 py-2 rounded hover:bg-green-600 transition">
                        <i class="fab fa-whatsapp"></i> واتساب
                    </a>
                    @if(!empty($globalSettings['telegram_url']))
                    <a href="{{ $globalSettings['telegram_url'] }}" target="_blank" rel="noopener nofollow"
                       class="flex items-center gap-2 bg-sky-400 text-white text-xs px-4 py-2 rounded hover:bg-sky-500 transition">
                        <i class="fab fa-telegram-plane"></i> تيليغرام
                    </a>
                    @endif
                    <button onclick="copyLink('{{ url($post->slug) }}')"
                            class="flex items-center gap-2 bg-gray-200 text-gray-700 text-xs px-4 py-2 rounded hover:bg-gray-300 transition">
                        <i class="fas fa-link"></i> نسخ الرابط
                    </button>
                </div>
            </div>

        </div>
        {{-- END UNIFIED CARD --}}

        {{-- Tags --}}
        @if($post->tags->count())
        <div class="bg-white border border-gray-200 rounded-xl p-3 mb-4 flex flex-wrap items-center gap-2">
            <span class="text-xs text-gray-500 font-medium"><i class="fas fa-tags text-[#30A38A]"></i> الوسوم:</span>
            @foreach($post->tags as $tag)
            <a href="{{ route('tag.show', $tag->slug) }}"
               class="bg-gray-100 hover:bg-[#30A38A] hover:text-white text-gray-600 text-xs px-3 py-1 rounded-full transition">
                {{ $tag->name }}
            </a>
            @endforeach
        </div>
        @endif

        {{-- Related Posts --}}
        @if($relatedPosts->count())
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-4">
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                <i class="fas fa-th-large text-[#30A38A]"></i>
                <h2 class="font-bold text-gray-900 text-sm">مقالات ذات صلة</h2>
            </div>
            <div class="px-4">
                @foreach($relatedPosts as $related)
                <x-front.post-card :post="$related" variant="list" />
                @endforeach
            </div>
        </div>
        @endif

        {{-- Comments --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                <i class="fas fa-comments text-[#30A38A]"></i>
                <h2 class="font-bold text-gray-900 text-sm">التعليقات ({{ $post->approvedComments->count() }})</h2>
            </div>
            @forelse($post->approvedComments as $comment)
            <div class="border-b border-gray-100 last:border-0 p-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-9 h-9 bg-[#30A38A] rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ mb_substr($comment->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ $comment->name }}</p>
                        <p class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <p class="text-gray-700 text-sm leading-relaxed mr-12">{{ $comment->body }}</p>
            </div>
            @empty
            <p class="text-gray-500 text-sm p-4">لا توجد تعليقات بعد. كن أول من يعلّق!</p>
            @endforelse
            <div class="border-t border-gray-200 p-4">
                @if(session('comment_success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4 text-sm">{{ session('comment_success') }}</div>
                @endif
                <h3 class="font-bold text-gray-800 mb-3 text-sm">أضف تعليقاً</h3>
                <form action="{{ route('comments.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="الاسم *"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-[#30A38A] @error('name') border-red-500 @enderror">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="البريد الإلكتروني (اختياري)"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-[#30A38A]">
                        </div>
                    </div>
                    <div>
                        <textarea name="body" rows="4" required placeholder="اكتب تعليقك هنا..."
                                  class="w-full px-3 py-2.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-[#30A38A] @error('body') border-red-500 @enderror">{{ old('body') }}</textarea>
                        @error('body')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="bg-[#30A38A] hover:bg-[#268a74] text-white font-bold py-2.5 px-8 rounded text-sm transition">
                        <i class="fas fa-paper-plane ml-1"></i> إرسال التعليق
                    </button>
                </form>
            </div>
        </div>

    </article>

    {{-- ═══ Sidebar ═══ --}}
    <div class="hidden lg:block w-96 flex-shrink-0">
        <x-front.sidebar :trending="$sidebarTrending" :tags="$sidebarTags" />
    </div>

</div>
@endsection

@section('scripts')
<script>
function trackDownload(postId) {
    fetch('/download/click', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ post_id: postId, source: 'article_button' })
    });
}
function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => alert('تم نسخ الرابط!'));
}
function ratingWidget(postId, avg, count, userRating) {
    return {
        postId, avgRating: avg, ratingCount: count, userRating, hover: 0, message: '',
        async rate(score) {
            if (this.userRating) return;
            const res = await fetch('/rate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ post_id: this.postId, score })
            });
            const data = await res.json();
            if (data.success) {
                this.userRating = score;
                this.avgRating = data.avg;
                this.ratingCount = data.count;
                this.message = data.message;
            }
        }
    }
}
</script>
@endsection
