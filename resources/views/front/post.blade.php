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

@section('content')
<div class="flex gap-5">

    {{-- ═══════════════════════
         Main Content (RIGHT in RTL)
    ═══════════════════════ --}}
    <article class="flex-1 min-w-0">

        {{-- Breadcrumb --}}
        <nav aria-label="مسار التنقل" class="mb-3 text-xs text-gray-500 flex flex-wrap items-center gap-1">
            <a href="{{ url('/') }}" class="hover:text-[#30A38A] transition">
                <i class="fas fa-home text-[10px]"></i> الرئيسية
            </a>
            @if($post->category)
            <i class="fas fa-angle-left text-[10px] text-gray-400"></i>
            <a href="{{ route('category.show', $post->category->slug) }}" class="hover:text-[#30A38A] transition">
                {{ $post->category->name }}
            </a>
            @endif
            <i class="fas fa-angle-left text-[10px] text-gray-400"></i>
            <span class="text-gray-700 truncate max-w-xs">{{ $post->title }}</span>
        </nav>

        {{-- Title + Date --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
            <div class="p-4 pb-3">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 leading-snug mb-2">
                    <i class="fas fa-download text-[#30A38A] text-base ml-1"></i>
                    {{ $post->title }}
                </h1>
                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500">
                    @if($post->published_at)
                    <span class="flex items-center gap-1">
                        <i class="fas fa-calendar-alt text-[#30A38A]"></i>
                        {{ $post->published_at->format('Y/m/d') }}
                    </span>
                    @endif
                    @if($post->category)
                    <a href="{{ route('category.show', $post->category->slug) }}"
                       class="flex items-center gap-1 hover:text-[#30A38A] transition">
                        <i class="fas fa-folder text-[#30A38A]"></i>
                        {{ $post->category->name }}
                    </a>
                    @endif
                    <span class="flex items-center gap-1">
                        <i class="fas fa-eye text-[#30A38A]"></i>
                        {{ number_format($post->views) }} مشاهدة
                    </span>
                </div>
            </div>

            {{-- Download Button --}}
            @if($post->downloadLinks->count())
            <div class="px-4 pb-4">
                <a href="{{ route('download.show', $post->slug) }}" target="_blank"
                   onclick="trackDownload({{ $post->id }})"
                   class="flex items-center justify-center gap-3 bg-green-500 hover:bg-green-600 text-white font-bold text-lg py-4 px-10 rounded-lg transition w-full shadow-sm">
                    <i class="fas fa-download text-xl"></i>
                    DOWNLOAD
                </a>
            </div>
            @endif
        </div>

        {{-- Featured Image --}}
        @if($post->featured_image)
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
            <img src="{{ asset('storage/'.$post->featured_image) }}"
                 alt="{{ $post->title }}"
                 class="w-full object-cover"
                 width="800" height="450"
                 loading="eager">
        </div>
        @endif

        {{-- Header Ad --}}
        @adslot('header_ad')

        {{-- Main Content Body --}}
        @if($post->excerpt || $post->content)
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
            <div class="p-5">
                @if($post->excerpt)
                <p class="text-gray-700 leading-loose mb-4 text-sm">{{ strip_tags($post->excerpt) }}</p>
                @endif
                @if($post->content)
                <div class="prose prose-sm max-w-none prose-headings:text-gray-900 prose-headings:font-bold prose-p:text-gray-700 prose-p:leading-loose prose-a:text-[#30A38A] prose-img:rounded prose-img:mx-auto">
                    {!! $post->content !!}
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- In-Content Ad --}}
        @adslot('in_content_ad')

        {{-- Specs / معلومات التحميل --}}
        @php
            $hasSpecs = $post->version || $post->developer || $post->file_size || $post->release_date || $post->updated_date;
        @endphp
        @if($hasSpecs)
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                <i class="fas fa-info-circle text-[#30A38A]"></i>
                <h2 class="font-bold text-gray-900 text-sm">معلومات حول تحميل {{ $post->title }}</h2>
            </div>
            <ul class="p-4 space-y-2">
                @if($post->version)
                <li class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="fas fa-check-circle text-[#30A38A] text-xs flex-shrink-0"></i>
                    <span>الإصدار: <strong>{{ $post->version }}</strong></span>
                </li>
                @endif
                @if($post->developer)
                <li class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="fas fa-check-circle text-[#30A38A] text-xs flex-shrink-0"></i>
                    <span>المطور: <strong>{{ $post->developer }}</strong></span>
                </li>
                @endif
                @if($post->file_size)
                <li class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="fas fa-check-circle text-[#30A38A] text-xs flex-shrink-0"></i>
                    <span>حجم الملف: <strong>{{ $post->file_size }}</strong></span>
                </li>
                @endif
                <li class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="fas fa-check-circle text-[#30A38A] text-xs flex-shrink-0"></i>
                    <span>المنصة: <strong>{{ $post->platform_ar }}</strong></span>
                </li>
                @if($post->release_date)
                <li class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="fas fa-check-circle text-[#30A38A] text-xs flex-shrink-0"></i>
                    <span>تاريخ الإصدار: <strong>{{ $post->release_date->format('Y/m/d') }}</strong></span>
                </li>
                @endif
                @if($post->updated_date)
                <li class="flex items-center gap-2 text-sm text-gray-700">
                    <i class="fas fa-check-circle text-[#30A38A] text-xs flex-shrink-0"></i>
                    <span>آخر تحديث: <strong>{{ $post->updated_date->format('Y/m/d') }}</strong></span>
                </li>
                @endif
            </ul>
        </div>
        @endif

        {{-- Features --}}
        @if($post->features)
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                <i class="fas fa-star text-[#30A38A]"></i>
                <h2 class="font-bold text-gray-900 text-sm">مميزات {{ $post->title }}</h2>
            </div>
            <div class="p-4">
                @foreach(array_filter(explode("\n", $post->features)) as $line)
                <div class="flex items-start gap-2 mb-2">
                    <i class="fas fa-check text-[#30A38A] text-xs mt-1 flex-shrink-0"></i>
                    <span class="text-sm text-gray-700">{{ trim($line) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- System Requirements --}}
        @if($post->system_requirements)
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                <i class="fas fa-desktop text-[#30A38A]"></i>
                <h2 class="font-bold text-gray-900 text-sm">متطلبات تشغيل {{ $post->title }} للكمبيوتر</h2>
            </div>
            <div class="p-4">
                @foreach(array_filter(explode("\n", $post->system_requirements)) as $line)
                <div class="flex items-start gap-2 mb-2">
                    <i class="fas fa-check text-[#30A38A] text-xs mt-1 flex-shrink-0"></i>
                    <span class="text-sm text-gray-700">{{ trim($line) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Pros & Cons --}}
        @if($post->pros || $post->cons)
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                <i class="fas fa-balance-scale text-[#30A38A]"></i>
                <h2 class="font-bold text-gray-900 text-sm">المميزات والعيوب</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-0 divide-x-0 md:divide-x divide-y md:divide-y-0 divide-gray-200">
                @if($post->pros)
                <div class="p-4">
                    <h3 class="font-bold text-green-600 mb-2 flex items-center gap-1 text-sm">
                        <i class="fas fa-thumbs-up"></i> المميزات
                    </h3>
                    <div class="text-sm text-gray-700 space-y-1">{!! nl2br(e($post->pros)) !!}</div>
                </div>
                @endif
                @if($post->cons)
                <div class="p-4">
                    <h3 class="font-bold text-red-500 mb-2 flex items-center gap-1 text-sm">
                        <i class="fas fa-thumbs-down"></i> العيوب
                    </h3>
                    <div class="text-sm text-gray-700 space-y-1">{!! nl2br(e($post->cons)) !!}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- What's New --}}
        @if($post->whats_new)
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                <i class="fas fa-bell text-[#30A38A]"></i>
                <h2 class="font-bold text-gray-900 text-sm">ما الجديد في هذا الإصدار</h2>
            </div>
            <div class="p-4 text-sm text-gray-700 prose max-w-none">{!! nl2br(e($post->whats_new)) !!}</div>
        </div>
        @endif

        {{-- Screenshots placeholder (for future gallery feature) --}}
        {{-- After adding gallery_images JSON field to posts table, display them here --}}

        {{-- YouTube Video --}}
        @if($post->youtube_url)
        @php
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $post->youtube_url, $m);
            $ytId = $m[1] ?? null;
        @endphp
        @if($ytId)
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                <i class="fab fa-youtube text-red-500"></i>
                <h2 class="font-bold text-gray-900 text-sm">فيديو شرح من داخل اللعبة</h2>
            </div>
            <div class="aspect-video">
                <iframe src="https://www.youtube.com/embed/{{ $ytId }}"
                        title="{{ $post->title }} - فيديو"
                        class="w-full h-full"
                        allowfullscreen loading="lazy"></iframe>
            </div>
        </div>
        @endif
        @endif

        {{-- Star Rating --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4"
             x-data="ratingWidget({{ $post->id }}, {{ $post->average_rating }}, {{ $post->ratings_count }}, {{ $userRating ?? 'null' }})">
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                <i class="fas fa-star text-yellow-400"></i>
                <h2 class="font-bold text-gray-900 text-sm">تقييم اللعبة</h2>
            </div>
            <div class="p-4 flex items-center gap-6">
                <div class="text-center border-l pl-6">
                    <p class="text-4xl font-bold text-gray-800" x-text="avgRating.toFixed(1)"></p>
                    <p class="text-xs text-gray-500 mt-1"><span x-text="ratingCount"></span> تقييم</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-2">أضف تقييمك:</p>
                    <div class="flex gap-1">
                        <template x-for="star in 5" :key="star">
                            <button @click="rate(star)"
                                    class="text-2xl transition"
                                    :class="star <= (hover || userRating || avgRating) ? 'text-yellow-400' : 'text-gray-300'"
                                    @mouseenter="hover = star" @mouseleave="hover = 0">★</button>
                        </template>
                    </div>
                    <p x-show="message" x-text="message" class="text-green-600 text-xs mt-1"></p>
                </div>
            </div>
        </div>

        {{-- Tags --}}
        @if($post->tags->count())
        <div class="bg-white border border-gray-200 rounded p-3 mb-4 flex flex-wrap items-center gap-2">
            <span class="text-xs text-gray-500 font-medium">
                <i class="fas fa-tags text-[#30A38A]"></i> الوسوم:
            </span>
            @foreach($post->tags as $tag)
            <a href="{{ route('tag.show', $tag->slug) }}"
               class="bg-gray-100 hover:bg-[#30A38A] hover:text-white text-gray-600 text-xs px-3 py-1 rounded-full transition">
                {{ $tag->name }}
            </a>
            @endforeach
        </div>
        @endif

        {{-- Social Share --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                <i class="fas fa-share-alt text-[#30A38A]"></i>
                <h2 class="font-bold text-gray-900 text-sm">مشاركة المقال</h2>
            </div>
            <div class="p-3 flex flex-wrap gap-2">
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

        {{-- Related Posts --}}
        @if($relatedPosts->count())
        <div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
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
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                <i class="fas fa-comments text-[#30A38A]"></i>
                <h2 class="font-bold text-gray-900 text-sm">
                    التعليقات ({{ $post->approvedComments->count() }})
                </h2>
            </div>

            {{-- Comments List --}}
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

            {{-- Comment Form --}}
            <div class="border-t border-gray-200 p-4">
                @if(session('comment_success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4 text-sm">
                    {{ session('comment_success') }}
                </div>
                @endif
                <h3 class="font-bold text-gray-800 mb-3 text-sm">أضف تعليقاً</h3>
                <form action="{{ route('comments.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   placeholder="الاسم *"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-[#30A38A] @error('name') border-red-500 @enderror">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="البريد الإلكتروني (اختياري)"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-[#30A38A]">
                        </div>
                    </div>
                    <div>
                        <textarea name="body" rows="4" required
                                  placeholder="اكتب تعليقك هنا..."
                                  class="w-full px-3 py-2.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-[#30A38A] @error('body') border-red-500 @enderror">{{ old('body') }}</textarea>
                        @error('body')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit"
                            class="bg-[#30A38A] hover:bg-[#268a74] text-white font-bold py-2.5 px-8 rounded text-sm transition">
                        <i class="fas fa-paper-plane ml-1"></i> إرسال التعليق
                    </button>
                </form>
            </div>
        </div>

    </article>

    {{-- ═══════════════════════
         Sidebar (LEFT in RTL)
    ═══════════════════════ --}}
    <div class="hidden lg:block w-72 flex-shrink-0">
        <x-front.sidebar :trending="$sidebarTrending" :tags="$sidebarTags" />
    </div>

</div>
@endsection

@section('scripts')
<script>
function trackDownload(postId) {
    fetch('/download/click', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
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
