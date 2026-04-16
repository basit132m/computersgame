@extends('layouts.admin')
@section('title', 'إضافة مقال جديد — Add New Post')

@section('head')
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
    content_style: 'body { font-family: Tajawal, Arial, sans-serif; font-size: 16px; direction: rtl; text-align: right; padding: 12px; } h2 { font-size: 1.25rem; font-weight: 700; border-bottom: 2px solid #30A38A; padding-bottom: 4px; margin-top: 1.5rem; } h3 { font-size: 1.1rem; font-weight: 600; margin-top: 1.25rem; }',
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
    setup: editor => { editor.on('change', () => editor.save()); }
});
</script>
@endsection

@section('content')
<form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" x-data="postForm()">
    @csrf

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- ═══════════════ Main Content ═══════════════ --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Title --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-2">
                    عنوان المقال <span class="en-hint">Post Title</span>
                    <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full px-4 py-3 border rounded-xl text-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="عنوان واضح وجذاب — Clear & catchy title">
            </div>

            {{-- Excerpt --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <label class="font-medium text-gray-700">
                        المقتطف <span class="en-hint">Excerpt — Short description shown in listings</span>
                    </label>
                    <button type="button" @click="generateAI('excerpt')" class="text-xs text-purple-600 hover:text-purple-700 flex items-center gap-1">
                        <span x-show="!loading.excerpt">✨ توليد تلقائي (Auto-generate)</span>
                        <span x-show="loading.excerpt">جاري التوليد...</span>
                    </button>
                </div>
                <textarea name="excerpt" id="excerpt" rows="3"
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                    placeholder="وصف مختصر للمقال...">{{ old('excerpt') }}</textarea>
            </div>

            {{-- Focus Keyword --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-2">
                    الكلمة المفتاحية المحورية <span class="en-hint">Focus Keyword — used in article headings & sections</span>
                </label>
                <input type="text" name="focus_keyword" value="{{ old('focus_keyword') }}"
                    class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="مثال: تحميل لعبة GTA V — e.g. GTA V download">
                <p class="text-xs text-gray-400 mt-1">تظهر في عناوين H2 والأقسام الرئيسية للمقال — Appears in H2 titles and main article sections</p>
            </div>

            {{-- TinyMCE Content --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-2">
                    المحتوى الكامل <span class="en-hint">Full Content (use H2/H3 for headings)</span>
                </label>
                <textarea name="content" id="content">{{ old('content') }}</textarea>
            </div>

            {{-- Game Info --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">
                    معلومات اللعبة / البرنامج
                    <span class="en-hint">Game / Software Information</span>
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">الإصدار <span class="en-hint">Version</span></label>
                        <input type="text" name="version" value="{{ old('version') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. 1.0.0">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">المطور <span class="en-hint">Developer</span></label>
                        <input type="text" name="developer" value="{{ old('developer') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">حجم الملف <span class="en-hint">File Size</span></label>
                        <input type="text" name="file_size" value="{{ old('file_size') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. 2.5 GB">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">اللغة <span class="en-hint">Game Language</span></label>
                        <input type="text" name="game_language" value="{{ old('game_language') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="العربية / English">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">المنصة <span class="en-hint">Platform</span></label>
                        <select name="platform" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pc">كمبيوتر — PC</option>
                            <option value="android">أندرويد — Android</option>
                            <option value="ios">iOS</option>
                            <option value="mac">ماك — Mac</option>
                            <option value="all">جميع المنصات — All Platforms</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">تاريخ الإصدار <span class="en-hint">Release Date</span></label>
                        <input type="date" name="release_date" value="{{ old('release_date') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">آخر تحديث <span class="en-hint">Last Updated</span></label>
                        <input type="date" name="updated_date" value="{{ old('updated_date') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-600 mb-1 block">رابط يوتيوب <span class="en-hint">YouTube URL</span></label>
                        <input type="url" name="youtube_url" value="{{ old('youtube_url') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://youtube.com/watch?v=...">
                    </div>
                </div>
            </div>

            {{-- System Requirements --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">
                    متطلبات التشغيل <span class="en-hint">System Requirements</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach([
                        ['sys_req_os',      'نظام التشغيل',      'OS',              'Windows 10 64-bit'],
                        ['sys_req_cpu',     'المعالج',           'CPU',             'Intel Core i5-8400'],
                        ['sys_req_gpu',     'كارت الشاشة',       'GPU',             'NVIDIA GTX 970 4GB'],
                        ['sys_req_ram',     'الذاكرة RAM',        'RAM',             '8 GB'],
                        ['sys_req_storage', 'مساحة التخزين',     'Storage',         '70 GB'],
                        ['sys_req_software','البرامج المطلوبة',  'Required Software','DirectX 12'],
                    ] as [$name, $ar, $en, $ph])
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">{{ $ar }} <span class="en-hint">{{ $en }}</span></label>
                        <input type="text" name="{{ $name }}" value="{{ old($name) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="{{ $ph }}">
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Detailed Sections --}}
            <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
                <h3 class="font-bold text-gray-800">
                    أقسام تفصيلية <span class="en-hint">Detailed Sections — one item per line</span>
                </h3>
                <div>
                    <label class="text-sm font-medium text-gray-600 mb-1 block">المميزات <span class="en-hint">Features</span></label>
                    <textarea name="features" rows="3"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="اكتب كل ميزة في سطر — one feature per line">{{ old('features') }}</textarea>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600 mb-1 block">ما الجديد <span class="en-hint">What's New</span></label>
                    <textarea name="whats_new" rows="3"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Changes in this version...">{{ old('whats_new') }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">المميزات (إيجابيات) <span class="en-hint">Pros</span></label>
                        <textarea name="pros" rows="3"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="One pro per line...">{{ old('pros') }}</textarea>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">العيوب (سلبيات) <span class="en-hint">Cons</span></label>
                        <textarea name="cons" rows="3"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="One con per line...">{{ old('cons') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Download Links --}}
            <div class="bg-white rounded-xl p-5 shadow-sm" x-data="downloadLinksForm()">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-800">
                        <i class="fas fa-download text-blue-500 ml-1"></i>
                        روابط التحميل <span class="en-hint">Download Links</span>
                    </h3>
                    <button type="button" @click="addLink()"
                        class="text-xs bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                        <i class="fas fa-plus"></i> إضافة رابط — Add Link
                    </button>
                </div>

                <template x-for="(link, idx) in links" :key="idx">
                    <div class="border border-gray-200 rounded-lg p-3 mb-3 bg-gray-50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-gray-500" x-text="'رابط #' + (idx + 1)"></span>
                            <button type="button" @click="removeLink(idx)"
                                class="text-red-400 hover:text-red-600 text-xs">
                                <i class="fas fa-trash"></i> حذف
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">التسمية <span class="en-hint">Label</span></label>
                                <input type="text" :name="'download_links[' + idx + '][label]'" x-model="link.label"
                                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="تحميل مباشر — Direct Download">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">رابط التحميل <span class="en-hint">Download URL *</span></label>
                                <input type="url" :name="'download_links[' + idx + '][url]'" x-model="link.url"
                                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="https://...">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">المنصة <span class="en-hint">Platform</span></label>
                                <select :name="'download_links[' + idx + '][platform]'" x-model="link.platform"
                                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="pc">كمبيوتر — PC</option>
                                    <option value="android">أندرويد — Android</option>
                                    <option value="ios">iOS</option>
                                    <option value="mac">ماك — Mac</option>
                                    <option value="all">الكل — All</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">حجم الملف <span class="en-hint">File Size</span></label>
                                <input type="text" :name="'download_links[' + idx + '][file_size]'" x-model="link.file_size"
                                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="e.g. 2.5 GB">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs text-gray-500 mb-1 block">الإصدار <span class="en-hint">Version</span></label>
                                <input type="text" :name="'download_links[' + idx + '][version]'" x-model="link.version"
                                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="e.g. 1.0.0">
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="links.length === 0" class="text-center py-6 text-gray-400 text-sm">
                    <i class="fas fa-download text-2xl mb-2 block"></i>
                    لم تُضف روابط بعد — No links added yet<br>
                    <span class="text-xs">يمكن إضافة وإدارة الروابط بشكل متقدم من صفحة التعديل — Advanced management available on the edit page</span>
                </div>
            </div>

            {{-- SEO Fields --}}
            <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
                <h3 class="font-bold text-gray-800">
                    إعدادات SEO <span class="en-hint">SEO Settings</span>
                </h3>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-sm font-medium text-gray-600">
                            عنوان الـ Meta <span class="en-hint">Meta Title (max 60 chars)</span>
                        </label>
                        <button type="button" @click="generateAI('meta_title')" class="text-xs text-purple-600 hover:text-purple-700">
                            <span x-show="!loading.meta_title">✨ Auto-generate</span>
                            <span x-show="loading.meta_title">Loading...</span>
                        </button>
                    </div>
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-sm font-medium text-gray-600">
                            وصف الـ Meta <span class="en-hint">Meta Description (max 155 chars)</span>
                        </label>
                        <button type="button" @click="generateAI('meta_description')" class="text-xs text-purple-600 hover:text-purple-700">
                            <span x-show="!loading.meta_description">✨ Auto-generate</span>
                            <span x-show="loading.meta_description">Loading...</span>
                        </button>
                    </div>
                    <textarea name="meta_description" id="meta_description" rows="2"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('meta_description') }}</textarea>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-sm font-medium text-gray-600">
                            الكلمات المفتاحية <span class="en-hint">Meta Keywords (comma-separated)</span>
                        </label>
                        <button type="button" @click="generateAI('meta_keywords')" class="text-xs text-purple-600 hover:text-purple-700">
                            <span x-show="!loading.meta_keywords">✨ Auto-generate</span>
                            <span x-show="loading.meta_keywords">Loading...</span>
                        </button>
                    </div>
                    <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords') }}"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="keyword1, keyword2, keyword3...">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">Robots <span class="en-hint">Search engine crawling</span></label>
                        <input type="text" name="robots" value="{{ old('robots', 'index, follow') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">Schema Type <span class="en-hint">Structured data type</span></label>
                        <select name="schema_type" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(['SoftwareApplication', 'Article', 'Review', 'HowTo', 'ItemList'] as $st)
                            <option value="{{ $st }}" {{ old('schema_type') === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════ Sidebar ═══════════════ --}}
        <div class="space-y-4">

            {{-- Publish Controls --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">النشر <span class="en-hint">Publishing</span></h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">الحالة <span class="en-hint">Status</span></label>
                        <select name="status" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="draft">مسودة — Draft</option>
                            <option value="pending">قيد المراجعة — Pending Review</option>
                            <option value="published">نشر الآن — Published</option>
                            <option value="scheduled">جدولة — Scheduled</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">النوع <span class="en-hint">Post Type</span></label>
                        <select name="type" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="game">لعبة — Game</option>
                            <option value="software">برنامج — Software</option>
                            <option value="apk">تطبيق أندرويد — Android App</option>
                            <option value="blog">مقال — Article</option>
                            <option value="tutorial">شرح — Tutorial</option>
                            <option value="listicle">قائمة — List</option>
                            <option value="review">مراجعة — Review</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">Slug <span class="en-hint">URL path — leave blank to auto-generate</span></label>
                        <input type="text" name="slug" value="{{ old('slug') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                            placeholder="auto-generated-from-title">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">تاريخ النشر <span class="en-hint">Publish Date</span></label>
                        <input type="datetime-local" name="published_at" value="{{ old('published_at') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-4 rounded-lg transition text-sm">
                        حفظ — Save
                    </button>
                    <a href="{{ route('admin.posts.index') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-4 rounded-lg transition text-sm">
                        إلغاء — Cancel
                    </a>
                </div>
            </div>

            {{-- Category --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-3">
                    التصنيف <span class="en-hint">Category</span>
                </label>
                <select name="category_id" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- اختر تصنيفاً — Select category --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->parent ? '↳ ' : '' }}{{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Featured Image --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-3">
                    الصورة الرئيسية <span class="en-hint">Featured Image (thumbnail for listings)</span>
                </label>
                <input type="file" name="featured_image" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-400 mt-2">Auto-converted to WebP 800×450</p>
            </div>

            {{-- Banner Image --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-3">
                    <i class="fas fa-image text-[#30A38A] ml-1"></i>
                    صورة البانر <span class="en-hint">Banner Image — large image shown at top of article</span>
                </label>
                <input type="file" name="banner_image" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                <p class="text-xs text-gray-400 mt-2">Displayed as full-width banner at top of article</p>
            </div>

            {{-- Gallery Images --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-3">
                    <i class="fas fa-images text-purple-500 ml-1"></i>
                    صور المعرض / لقطات الشاشة <span class="en-hint">Gallery / Screenshots — shown after YouTube video</span>
                </label>
                <input type="file" name="gallery_images[]" accept="image/*" multiple
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                <p class="text-xs text-gray-400 mt-2">اختر أكثر من صورة بالضغط المطوّل — Hold Ctrl/Cmd to select multiple</p>
            </div>

            {{-- Tags --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-3">
                    الوسوم <span class="en-hint">Tags (comma-separated)</span>
                </label>
                <input type="text" name="tags" value="{{ old('tags') }}"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="tag1, tag2, tag3">
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
function downloadLinksForm() {
    return {
        links: [],
        addLink() {
            this.links.push({ label: 'تحميل مباشر', url: '', platform: 'pc', file_size: '', version: '' });
        },
        removeLink(idx) {
            this.links.splice(idx, 1);
        }
    };
}

function postForm() {
    return {
        loading: { meta_title: false, meta_description: false, meta_keywords: false, excerpt: false },
        async generateAI(action) {
            this.loading[action] = true;
            const title = document.querySelector('[name=title]')?.value || '';
            const category = document.querySelector('[name=category_id] option:checked')?.text || '';
            const type = document.querySelector('[name=type]')?.value || '';
            const content = tinymce.get('content')?.getContent({ format: 'text' }) || '';
            try {
                const res = await fetch('/admin/ai/generate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
                    body: JSON.stringify({ action, title, category, type, content })
                });
                const data = await res.json();
                if (data.success) { const el = document.getElementById(action); if (el) el.value = data.result; }
                else alert('حدث خطأ — Error occurred, please try again');
            } catch { alert('حدث خطأ — Network error'); }
            finally { this.loading[action] = false; }
        }
    }
}
</script>
@endsection
