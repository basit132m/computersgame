@extends('layouts.admin')
@section('title', 'تعديل — Edit: ' . $post->title)

@push('head')
<script src="https://cdn.tiny.cloud/1/nf5vqpeni2pg38rvx6wdx4s7fc2t92tkm9lclf528l48j1dp/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#content',
    directionality: 'rtl',
    height: 500,
    menubar: 'file edit view insert format tools table help',
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'directionality'
    ],
    toolbar: [
        'undo redo | blocks | bold italic forecolor backcolor |',
        'alignright aligncenter alignleft alignjustify | ltr rtl |',
        'bullist numlist | outdent indent | link image media |',
        'table | code fullscreen | help'
    ].join(' '),
    content_style: 'body { font-family: Tajawal, Arial, sans-serif; font-size: 16px; direction: rtl; text-align: right; padding: 12px; }',
    images_upload_url: '{{ route("admin.media.upload") }}',
    images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("admin.media.upload") }}');
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name=csrf-token]').content);
        xhr.upload.onprogress = e => { if (e.lengthComputable) progress(e.loaded / e.total * 100); };
        xhr.onload = () => {
            const json = JSON.parse(xhr.responseText);
            if (xhr.status !== 200 || !json.url) { reject('فشل الرفع'); return; }
            resolve(json.url);
        };
        xhr.onerror = () => reject('خطأ في الشبكة');
        const data = new FormData();
        data.append('file', blobInfo.blob(), blobInfo.filename());
        xhr.send(data);
    }),
    setup: editor => {
        editor.on('change', () => editor.save());
    }
});
</script>
@endpush

@section('content')
<form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" x-data="postForm()">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Column --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Validation errors --}}
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- Title --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-2">عنوان المقال <span class="en-hint">Post Title</span> <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                    class="w-full px-4 py-3 border rounded-xl text-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Excerpt --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <label class="font-medium text-gray-700">المقتطف <span class="en-hint">Excerpt — short description</span></label>
                    <button type="button" @click="generateAI('excerpt')" class="text-xs text-purple-600 hover:text-purple-800 flex items-center gap-1 transition">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <span x-show="!loading.excerpt">توليد تلقائي</span>
                        <span x-show="loading.excerpt" class="flex items-center gap-1"><div class="w-3 h-3 border border-purple-600 border-t-transparent rounded-full animate-spin"></div> جاري...</span>
                    </button>
                </div>
                <textarea name="excerpt" id="excerpt" rows="3"
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

            {{-- TinyMCE Content --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-2">المحتوى الكامل <span class="en-hint">Full Content (main article body)</span></label>
                <textarea name="content" id="content">{{ old('content', $post->content) }}</textarea>
            </div>

            {{-- Game Info --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">معلومات اللعبة / البرنامج <span class="en-hint">Game / Software Info</span></h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">الإصدار <span class="en-hint">Version</span></label>
                        <input type="text" name="version" value="{{ old('version', $post->version) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="مثال: 2.5.1">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">المطور <span class="en-hint">Developer</span></label>
                        <input type="text" name="developer" value="{{ old('developer', $post->developer) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">حجم الملف <span class="en-hint">File Size</span></label>
                        <input type="text" name="file_size" value="{{ old('file_size', $post->file_size) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="مثال: 4.2 GB">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">المنصة <span class="en-hint">Platform</span></label>
                        <select name="platform" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(['pc' => 'كمبيوتر — PC', 'android' => 'أندرويد — Android', 'ios' => 'iOS', 'mac' => 'ماك — Mac', 'all' => 'جميع المنصات — All Platforms'] as $val => $label)
                            <option value="{{ $val }}" {{ old('platform', $post->platform) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">تاريخ الإصدار <span class="en-hint">Release Date</span></label>
                        <input type="date" name="release_date" value="{{ old('release_date', $post->release_date?->format('Y-m-d')) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">آخر تحديث <span class="en-hint">Last Updated</span></label>
                        <input type="date" name="updated_date" value="{{ old('updated_date', $post->updated_date?->format('Y-m-d')) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="col-span-2 md:col-span-3">
                        <label class="text-sm font-medium text-gray-600 mb-1 block">رابط يوتيوب <span class="en-hint">YouTube URL</span></label>
                        <input type="url" name="youtube_url" value="{{ old('youtube_url', $post->youtube_url) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://youtube.com/watch?v=...">
                    </div>
                </div>
            </div>

            {{-- Detailed Sections --}}
            <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
                <h3 class="font-bold text-gray-800">أقسام تفصيلية <span class="en-hint">Detailed Sections</span></h3>
                @foreach([
                    ['features', 'المميزات', 'Features'],
                    ['system_requirements', 'متطلبات التشغيل', 'System Requirements'],
                    ['whats_new', 'ما الجديد', "What's New"],
                ] as [$field, $label, $en])
                <div>
                    <label class="text-sm font-medium text-gray-600 mb-1 block">{{ $label }} <span class="en-hint">{{ $en }}</span></label>
                    <textarea name="{{ $field }}" rows="3"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old($field, $post->$field) }}</textarea>
                </div>
                @endforeach
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">المميزات (إيجابيات) <span class="en-hint">Pros</span></label>
                        <textarea name="pros" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('pros', $post->pros) }}</textarea>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">العيوب (سلبيات) <span class="en-hint">Cons</span></label>
                        <textarea name="cons" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('cons', $post->cons) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SEO Fields --}}
            <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    إعدادات SEO
                </h3>
                @foreach([
                    ['meta_title', 'عنوان Meta', 'Meta Title (max 60 chars)', 'text', 'meta_title'],
                    ['meta_description', 'وصف Meta', 'Meta Description (max 155 chars)', 'textarea', 'meta_description'],
                    ['meta_keywords', 'الكلمات المفتاحية', 'Meta Keywords (comma-separated)', 'text', 'meta_keywords'],
                ] as [$field, $label, $en, $inputType, $aiAction])
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-sm font-medium text-gray-600">{{ $label }} <span class="en-hint">{{ $en }}</span></label>
                        <button type="button" @click="generateAI('{{ $aiAction }}')"
                            class="text-xs text-purple-600 hover:text-purple-800 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span x-show="!loading['{{ $aiAction }}']">توليد تلقائي</span>
                            <span x-show="loading['{{ $aiAction }}']"><div class="w-3 h-3 border border-purple-600 border-t-transparent rounded-full animate-spin inline-block"></div></span>
                        </button>
                    </div>
                    @if($inputType === 'textarea')
                    <textarea name="{{ $field }}" id="{{ $field }}" rows="2"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old($field, $post->$field) }}</textarea>
                    @else
                    <input type="text" name="{{ $field }}" id="{{ $field }}" value="{{ old($field, $post->$field) }}"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @endif
                </div>
                @endforeach
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">Robots <span class="en-hint">Controls search engine crawling</span></label>
                        <input type="text" name="robots" value="{{ old('robots', $post->robots) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">Schema Type</label>
                        <select name="schema_type" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(['SoftwareApplication', 'Article', 'Review', 'HowTo', 'ItemList'] as $type)
                            <option value="{{ $type }}" {{ old('schema_type', $post->schema_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Download Links (Livewire) --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    روابط التحميل
                </h3>
                @livewire('download-links-reorder', ['postId' => $post->id])
            </div>

        </div>

        {{-- Sidebar Column --}}
        <div class="space-y-4">
            {{-- Publish --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">النشر <span class="en-hint">Publishing</span></h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">الحالة</label>
                        <select name="status" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(['draft' => 'مسودة — Draft', 'pending' => 'قيد المراجعة — Pending', 'published' => 'منشور — Published', 'scheduled' => 'مجدول — Scheduled'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $post->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">النوع</label>
                        <select name="type" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(['game' => 'لعبة — Game', 'software' => 'برنامج — Software', 'apk' => 'تطبيق أندرويد — Android App', 'blog' => 'مقال — Article', 'tutorial' => 'شرح — Tutorial', 'listicle' => 'قائمة — List', 'review' => 'مراجعة — Review'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type', $post->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">Slug <span class="en-hint">URL path</span></label>
                        <input type="text" name="slug" value="{{ old('slug', $post->slug) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">تاريخ النشر <span class="en-hint">Publish Date</span></label>
                        <input type="datetime-local" name="published_at"
                            value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-4 rounded-lg transition text-sm">حفظ التغييرات</button>
                    <a href="{{ url($post->slug) }}" target="_blank" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-4 rounded-lg transition text-sm">عرض</a>
                </div>
                <div class="mt-2 flex gap-2 text-xs text-gray-500 pt-2 border-t">
                    <span>مشاهدات: {{ number_format($post->views) }}</span>
                    <span>•</span>
                    <span>تحميلات: {{ number_format($post->downloads) }}</span>
                </div>
            </div>

            {{-- Category --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-3">التصنيف <span class="en-hint">Category</span></label>
                <select name="category_id" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- بدون تصنيف --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->parent ? '↳ ' : '' }}{{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Featured Image --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-3">الصورة الرئيسية <span class="en-hint">Featured Image</span></label>
                @if($post->featured_image)
                <div class="mb-3">
                    <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}"
                        class="w-full aspect-video object-cover rounded-lg" loading="lazy">
                </div>
                @endif
                <input type="file" name="featured_image" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-400 mt-1">سيتم التحويل تلقائياً إلى WebP (800×450)</p>
            </div>

            {{-- Tags --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-3">الوسوم <span class="en-hint">Tags (comma-separated)</span></label>
                <input type="text" name="tags"
                    value="{{ old('tags', $post->tags->pluck('name')->join(', ')) }}"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="وسم1, وسم2, وسم3">
                <p class="text-xs text-gray-400 mt-1">مفصولة بفواصل</p>
            </div>

            {{-- Post Stats --}}
            <div class="bg-white rounded-xl p-5 shadow-sm text-sm space-y-2 text-gray-600">
                <p><span class="font-medium">أُنشئ:</span> {{ $post->created_at->format('Y/m/d H:i') }}</p>
                <p><span class="font-medium">آخر تعديل:</span> {{ $post->updated_at->format('Y/m/d H:i') }}</p>
                @if($post->published_at)
                <p><span class="font-medium">نُشر:</span> {{ $post->published_at->format('Y/m/d H:i') }}</p>
                @endif
                <p><span class="font-medium">بواسطة:</span> {{ $post->author?->name ?? 'غير محدد' }}</p>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function postForm() {
    return {
        loading: {
            meta_title: false, meta_description: false,
            meta_keywords: false, excerpt: false,
        },
        async generateAI(action) {
            this.loading[action] = true;
            const title    = document.querySelector('[name=title]')?.value || '';
            const category = document.querySelector('[name=category_id] option:checked')?.text || '';
            const type     = document.querySelector('[name=type]')?.value || '';
            const content  = (typeof tinymce !== 'undefined' && tinymce.get('content'))
                ? tinymce.get('content').getContent({ format: 'text' }).substring(0, 1000)
                : '';
            try {
                const res  = await fetch('{{ route("admin.ai.generate") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ action, title, category, type, content }),
                });
                const data = await res.json();
                if (data.success) {
                    const el = document.getElementById(action);
                    if (el) el.value = data.result;
                } else { alert('حدث خطأ، يرجى المحاولة مرة أخرى'); }
            } catch { alert('حدث خطأ، يرجى المحاولة مرة أخرى'); }
            finally   { this.loading[action] = false; }
        }
    };
}
</script>
@endpush
