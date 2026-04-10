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
<div class="flex gap-6">
    {{-- Main Content --}}
    <article class="flex-1 min-w-0">
        {{-- Breadcrumb --}}
        <nav aria-label="مسار التنقل" class="mb-4 text-sm text-gray-500">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ url('/') }}" class="hover:text-blue-700">الرئيسية</a></li>
                @if($post->category)
                <li class="before:content-['/'] before:mx-1">
                    <a href="{{ route('category.show', $post->category->slug) }}" class="hover:text-blue-700">{{ $post->category->name }}</a>
                </li>
                @endif
                <li class="before:content-['/'] before:mx-1 text-gray-700 font-medium">{{ $post->title }}</li>
            </ol>
        </nav>

        {{-- Title --}}
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>

        {{-- Info Box --}}
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 grid grid-cols-2 md:grid-cols-3 gap-3">
            @if($post->version)
            <div class="text-sm">
                <span class="text-gray-500 block">الإصدار</span>
                <strong class="text-gray-800">{{ $post->version }}</strong>
            </div>
            @endif
            @if($post->developer)
            <div class="text-sm">
                <span class="text-gray-500 block">المطور</span>
                <strong class="text-gray-800">{{ $post->developer }}</strong>
            </div>
            @endif
            @if($post->file_size)
            <div class="text-sm">
                <span class="text-gray-500 block">حجم الملف</span>
                <strong class="text-gray-800">{{ $post->file_size }}</strong>
            </div>
            @endif
            <div class="text-sm">
                <span class="text-gray-500 block">المنصة</span>
                <strong class="text-gray-800">{{ $post->platform_ar }}</strong>
            </div>
            @if($post->release_date)
            <div class="text-sm">
                <span class="text-gray-500 block">تاريخ الإصدار</span>
                <strong class="text-gray-800">{{ $post->release_date->format('Y/m/d') }}</strong>
            </div>
            @endif
            @if($post->updated_date)
            <div class="text-sm">
                <span class="text-gray-500 block">آخر تحديث</span>
                <strong class="text-gray-800">{{ $post->updated_date->format('Y/m/d') }}</strong>
            </div>
            @endif
        </div>

        {{-- Featured Image --}}
        @if($post->featured_image)
        <figure class="mb-6">
            <img
                src="{{ asset('storage/'.$post->featured_image) }}"
                alt="{{ $post->title }}"
                class="w-full rounded-xl object-cover max-h-96"
                width="800" height="450"
                loading="eager"
            >
        </figure>
        @endif

        {{-- Ad before download --}}
        @adslot('before_download_ad')

        {{-- Download Button --}}
        @if($post->downloadLinks->count())
        <div class="mb-6 text-center">
            <a href="{{ route('download.show', $post->slug) }}" target="_blank"
                onclick="trackDownload({{ $post->id }})"
                class="inline-flex items-center gap-3 bg-green-600 hover:bg-green-700 text-white font-bold text-lg py-4 px-10 rounded-xl transition shadow-lg hover:shadow-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                تحميل مجاني
            </a>
        </div>
        @endif

        {{-- YouTube Embed --}}
        @if($post->youtube_url)
        <div class="mb-6">
            @php
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $post->youtube_url, $m);
                $ytId = $m[1] ?? null;
            @endphp
            @if($ytId)
            <div class="aspect-video rounded-xl overflow-hidden">
                <iframe
                    src="https://www.youtube.com/embed/{{ $ytId }}"
                    title="{{ $post->title }} - فيديو"
                    class="w-full h-full"
                    allowfullscreen
                    loading="lazy"
                ></iframe>
            </div>
            @endif
        </div>
        @endif

        {{-- Star Rating --}}
        <div class="bg-white rounded-xl border p-4 mb-6" x-data="ratingWidget({{ $post->id }}, {{ $post->average_rating }}, {{ $post->ratings_count }}, {{ $userRating ?? 'null' }})">
            <div class="flex items-center gap-4">
                <div>
                    <p class="text-sm text-gray-600 mb-1">تقييمك للمحتوى:</p>
                    <div class="flex gap-1">
                        <template x-for="star in 5" :key="star">
                            <button @click="rate(star)" class="text-2xl transition" :class="star <= (hover || userRating || avgRating) ? 'text-yellow-400' : 'text-gray-300'" @mouseenter="hover = star" @mouseleave="hover = 0">★</button>
                        </template>
                    </div>
                </div>
                <div class="text-center border-r pr-4 mr-4">
                    <p class="text-3xl font-bold text-gray-800" x-text="avgRating.toFixed(1)"></p>
                    <p class="text-sm text-gray-500"><span x-text="ratingCount"></span> تقييم</p>
                </div>
            </div>
            <p x-show="message" x-text="message" class="text-green-600 text-sm mt-2"></p>
        </div>

        {{-- Main Content Body --}}
        @if($post->content)
        <div class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-700 mb-6 bg-white rounded-xl p-6 border">
            {!! $post->content !!}
        </div>
        @endif

        {{-- In-content Ad 1 --}}
        @adslot('in_content_ad_1')

        {{-- Features --}}
        @if($post->features)
        <section class="bg-white rounded-xl border p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                المميزات
            </h2>
            <div class="text-gray-700 prose max-w-none">{!! nl2br(e($post->features)) !!}</div>
        </section>
        @endif

        {{-- System Requirements --}}
        @if($post->system_requirements)
        <section class="bg-white rounded-xl border p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">💻 متطلبات التشغيل</h2>
            <div class="text-gray-700 prose max-w-none">{!! nl2br(e($post->system_requirements)) !!}</div>
        </section>
        @endif

        {{-- In-content Ad 2 --}}
        @adslot('in_content_ad_2')

        {{-- What's New --}}
        @if($post->whats_new)
        <section class="bg-white rounded-xl border p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">🆕 ما الجديد</h2>
            <div class="text-gray-700 prose max-w-none">{!! nl2br(e($post->whats_new)) !!}</div>
        </section>
        @endif

        {{-- Pros and Cons --}}
        @if($post->pros || $post->cons)
        <section class="bg-white rounded-xl border p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">المميزات والعيوب</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($post->pros)
                <div class="bg-green-50 rounded-lg p-4">
                    <h3 class="font-bold text-green-700 mb-2">✅ المميزات</h3>
                    <div class="text-sm text-gray-700">{!! nl2br(e($post->pros)) !!}</div>
                </div>
                @endif
                @if($post->cons)
                <div class="bg-red-50 rounded-lg p-4">
                    <h3 class="font-bold text-red-600 mb-2">❌ العيوب</h3>
                    <div class="text-sm text-gray-700">{!! nl2br(e($post->cons)) !!}</div>
                </div>
                @endif
            </div>
        </section>
        @endif

        {{-- Tags --}}
        @if($post->tags->count())
        <div class="mb-6 flex flex-wrap gap-2">
            <span class="text-sm text-gray-500">الوسوم:</span>
            @foreach($post->tags as $tag)
            <a href="{{ route('tag.show', $tag->slug) }}" class="bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-700 text-xs px-3 py-1 rounded-full transition">{{ $tag->name }}</a>
            @endforeach
        </div>
        @endif

        {{-- Social Share --}}
        <div class="bg-white rounded-xl border p-4 mb-6">
            <p class="text-sm font-medium text-gray-700 mb-3">مشاركة المقال:</p>
            <div class="flex flex-wrap gap-2">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url($post->slug)) }}" target="_blank" rel="noopener nofollow"
                    class="flex items-center gap-2 bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    مشاركة على فيسبوك
                </a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url($post->slug)) }}" target="_blank" rel="noopener nofollow"
                    class="flex items-center gap-2 bg-sky-500 text-white text-sm px-4 py-2 rounded-lg hover:bg-sky-600 transition">
                    تويتر
                </a>
                <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . url($post->slug)) }}" target="_blank" rel="noopener nofollow"
                    class="flex items-center gap-2 bg-green-500 text-white text-sm px-4 py-2 rounded-lg hover:bg-green-600 transition">
                    واتساب
                </a>
                @if($globalSettings['telegram_url'])
                <a href="{{ $globalSettings['telegram_url'] }}" target="_blank" rel="noopener nofollow"
                    class="flex items-center gap-2 bg-blue-400 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-500 transition">
                    قناة تيليغرام
                </a>
                @endif
                <button onclick="copyLink('{{ url($post->slug) }}')" class="flex items-center gap-2 bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    نسخ الرابط
                </button>
            </div>
        </div>

        {{-- Related Posts --}}
        @if($relatedPosts->count())
        <section class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">مقالات ذات صلة</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($relatedPosts as $related)
                <x-front.post-card :post="$related" />
                @endforeach
            </div>
        </section>
        @endif

        {{-- Comments Section --}}
        <section class="bg-white rounded-xl border p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">
                التعليقات ({{ $post->approvedComments->count() }})
            </h2>

            {{-- Comments List --}}
            @forelse($post->approvedComments as $comment)
            <div class="border-b py-4 last:border-b-0">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-bold">
                        {{ mb_substr($comment->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $comment->name }}</p>
                        <p class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <p class="text-gray-700 text-sm leading-relaxed">{{ $comment->body }}</p>
            </div>
            @empty
            <p class="text-gray-500 text-sm mb-6">لا توجد تعليقات بعد. كن أول من يعلّق!</p>
            @endforelse

            {{-- Comment Form --}}
            @if(session('comment_success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
                {{ session('comment_success') }}
            </div>
            @endif

            <form action="{{ route('comments.store') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                <h3 class="font-bold text-gray-800">أضف تعليقاً</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الاسم <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                            placeholder="اسمك">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني (اختياري)</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="بريدك الإلكتروني">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">التعليق <span class="text-red-500">*</span></label>
                    <textarea name="body" rows="4" required
                        class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('body') border-red-500 @enderror"
                        placeholder="اكتب تعليقك هنا...">{{ old('body') }}</textarea>
                    @error('body')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-8 rounded-lg transition">
                    إرسال التعليق
                </button>
            </form>
        </section>
    </article>

    {{-- Sidebar --}}
    <div class="hidden lg:block w-80 flex-shrink-0">
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
        postId,
        avgRating: avg,
        ratingCount: count,
        userRating,
        hover: 0,
        message: '',
        async rate(score) {
            if (this.userRating) return;
            const res = await fetch('/rate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
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
