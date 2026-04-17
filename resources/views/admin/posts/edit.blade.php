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
    content_style: 'body { font-family: Almarai, Tahoma, Arial, sans-serif; font-size: 16px; direction: rtl; text-align: right; padding: 12px; } h2 { font-size: 1.25rem; font-weight: 700; border-bottom: 2px solid #30A38A; padding-bottom: 4px; margin-top: 1.5rem; } h3 { font-size: 1.1rem; font-weight: 600; margin-top: 1.25rem; }',
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
@endpush

@section('content')
<form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" x-data="postForm()">
    @csrf @method('PUT')

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- ═══════════════ Main Column ═══════════════ --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Title --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-2">عنوان المقال <span class="en-hint">Post Title</span> <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                    class="w-full px-4 py-3 border rounded-xl text-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Excerpt --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <label class="font-medium text-gray-700">المقتطف <span class="en-hint">Excerpt</span></label>
                    <button type="button" @click="generateAI('excerpt')" class="text-xs text-purple-600 hover:text-purple-800 flex items-center gap-1 transition">
                        <span x-show="!loading.excerpt">✨ توليد تلقائي</span>
                        <span x-show="loading.excerpt"><span class="inline-block w-3 h-3 border border-purple-600 border-t-transparent rounded-full animate-spin"></span> جاري...</span>
                    </button>
                </div>
                <textarea name="excerpt" id="excerpt" rows="3"
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

            {{-- Focus Keyword --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-2">
                    الكلمة المفتاحية المحورية <span class="en-hint">Focus Keyword — used in article headings & sections</span>
                </label>
                <input type="text" name="focus_keyword" value="{{ old('focus_keyword', $post->focus_keyword) }}"
                    class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="مثال: تحميل لعبة GTA V">
                <p class="text-xs text-gray-400 mt-1">تظهر في عناوين H2 والأقسام الرئيسية للمقال — Appears in H2 titles and main article sections</p>
            </div>

            {{-- TinyMCE Content --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-2">المحتوى الكامل <span class="en-hint">Full Content (use H2/H3 for headings)</span></label>
                <textarea name="content" id="content">{{ old('content', $post->content) }}</textarea>
            </div>

            {{-- Game Info --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">معلومات اللعبة / البرنامج <span class="en-hint">Game / Software Info</span></h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">الإصدار <span class="en-hint">Version</span></label>
                        <input type="text" name="version" value="{{ old('version', $post->version) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">المطور <span class="en-hint">Developer</span></label>
                        <input type="text" name="developer" value="{{ old('developer', $post->developer) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">حجم الملف <span class="en-hint">File Size</span></label>
                        <input type="text" name="file_size" value="{{ old('file_size', $post->file_size) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">اللغة <span class="en-hint">Game Language</span></label>
                        <input type="text" name="game_language" value="{{ old('game_language', $post->game_language) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="العربية / English">
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
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-600 mb-1 block">رابط يوتيوب <span class="en-hint">YouTube URL</span></label>
                        <input type="url" name="youtube_url" value="{{ old('youtube_url', $post->youtube_url) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://youtube.com/watch?v=...">
                    </div>
                </div>
            </div>

            {{-- System Requirements --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">متطلبات التشغيل <span class="en-hint">System Requirements</span></h3>
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
                        <input type="text" name="{{ $name }}" value="{{ old($name, $post->$name) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="{{ $ph }}">
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Detailed Sections --}}
            <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
                <h3 class="font-bold text-gray-800">أقسام تفصيلية <span class="en-hint">Detailed Sections</span></h3>
                @foreach([
                    ['features',    'المميزات',  'Features'],
                    ['whats_new',   'ما الجديد', "What's New"],
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
                <h3 class="font-bold text-gray-800">إعدادات SEO <span class="en-hint">SEO Settings</span></h3>
                @foreach([
                    ['meta_title',       'عنوان Meta',          'Meta Title (max 60 chars)',       'text',     'meta_title'],
                    ['meta_description', 'وصف Meta',            'Meta Description (max 155 chars)', 'textarea', 'meta_description'],
                    ['meta_keywords',    'الكلمات المفتاحية',   'Meta Keywords (comma-separated)',  'text',     'meta_keywords'],
                ] as [$field, $label, $en, $inputType, $aiAction])
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-sm font-medium text-gray-600">{{ $label }} <span class="en-hint">{{ $en }}</span></label>
                        <button type="button" @click="generateAI('{{ $aiAction }}')" class="text-xs text-purple-600 hover:text-purple-800 flex items-center gap-1">
                            <span x-show="!loading['{{ $aiAction }}']">✨ توليد تلقائي</span>
                            <span x-show="loading['{{ $aiAction }}']"><span class="inline-block w-3 h-3 border border-purple-600 border-t-transparent rounded-full animate-spin"></span></span>
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
                        <label class="text-sm font-medium text-gray-600 mb-1 block">Robots <span class="en-hint">Search engine crawling</span></label>
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

            {{-- Download Links --}}
            <div class="bg-white rounded-xl p-5 shadow-sm" x-data="{ newLinks: [] }">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-download text-blue-600"></i>
                    روابط التحميل <span class="en-hint">Download Links</span>
                </h3>

                {{-- Existing links --}}
                @if($post->downloadLinks->count())
                <div class="space-y-2 mb-4">
                    @foreach($post->downloadLinks as $link)
                    <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-800">{{ $link->label }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $link->url }}</p>
                            @if($link->file_size || $link->version)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $link->file_size }} {{ $link->version ? '— v'.$link->version : '' }}</p>
                            @endif
                        </div>
                        <button type="button"
                            onclick="deleteDownloadLink({{ $link->id }})"
                            class="text-red-400 hover:text-red-600 transition flex-shrink-0" title="حذف">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-400 text-sm mb-4">لا توجد روابط تحميل بعد — No download links yet</p>
                @endif

                {{-- Add new links — submitted with main form --}}
                <div class="border-t pt-4">
                    <p class="text-xs font-semibold text-gray-600 mb-3">إضافة روابط جديدة <span class="en-hint">Add New Links</span></p>
                    <template x-for="(link, idx) in newLinks" :key="idx">
                        <div class="grid grid-cols-2 gap-2 mb-2 p-3 bg-gray-50 rounded-lg relative">
                            <input type="text" :name="'new_download_links['+idx+'][label]'" x-model="link.label"
                                placeholder="العنوان* (مثل: جوجل درايف)"
                                class="col-span-2 px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="url" :name="'new_download_links['+idx+'][url]'" x-model="link.url"
                                placeholder="رابط التحميل*"
                                class="col-span-2 px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="text" :name="'new_download_links['+idx+'][file_size]'" x-model="link.file_size"
                                placeholder="حجم الملف"
                                class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="text" :name="'new_download_links['+idx+'][version]'" x-model="link.version"
                                placeholder="الإصدار"
                                class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="button" @click="newLinks.splice(idx, 1)"
                                class="absolute top-1 left-1 text-red-400 hover:text-red-600 text-xs p-1">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="newLinks.push({label:'',url:'',file_size:'',version:''})"
                        class="flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm font-medium mt-1">
                        <i class="fas fa-plus text-xs"></i> إضافة رابط — Add Link
                    </button>
                    <p class="text-xs text-gray-400 mt-2">أضف الروابط ثم اضغط "حفظ التغييرات" — Add links then click Save Changes</p>
                </div>
            </div>

        </div>

        {{-- ═══════════════ Sidebar Column ═══════════════ --}}
        <div class="space-y-4">

            {{-- Publish --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">النشر <span class="en-hint">Publishing</span></h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">الحالة <span class="en-hint">Status</span></label>
                        <select name="status" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(['draft' => 'مسودة — Draft', 'pending' => 'قيد المراجعة — Pending', 'published' => 'منشور — Published', 'scheduled' => 'مجدول — Scheduled'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $post->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">النوع <span class="en-hint">Post Type</span></label>
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
                <div class="mt-2 flex gap-3 text-xs text-gray-500 pt-2 border-t">
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
                <label class="block font-medium text-gray-700 mb-3">الصورة الرئيسية <span class="en-hint">Featured Image (thumbnail)</span></label>
                @if($post->featured_image)
                <div class="relative mb-3 group">
                    <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}"
                        class="w-full aspect-video object-cover rounded-lg" loading="lazy">
                    <div class="absolute top-1 left-1">
                        <button type="button"
                            onclick="deletePostImage('{{ route('admin.posts.image.remove', [$post, 'featured_image']) }}', 'حذف الصورة الرئيسية؟')"
                            class="bg-red-600 hover:bg-red-700 text-white text-xs px-2 py-1 rounded shadow">
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    </div>
                </div>
                @endif
                <input type="file" name="featured_image" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-400 mt-1">{{ $post->featured_image ? 'رفع صورة جديدة يستبدل الحالية — Upload to replace' : 'Auto-converted to WebP 800×450' }}</p>
            </div>

            {{-- Banner Image --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-3">
                    <i class="fas fa-image text-[#30A38A] ml-1"></i>
                    صورة البانر <span class="en-hint">Banner Image — top of article</span>
                </label>
                @if($post->banner_image)
                <div class="relative mb-3 group">
                    <img src="{{ asset('storage/'.$post->banner_image) }}" alt="banner"
                        class="w-full object-cover rounded-lg" style="max-height:120px;" loading="lazy">
                    <div class="absolute top-1 left-1">
                        <button type="button"
                            onclick="deletePostImage('{{ route('admin.posts.image.remove', [$post, 'banner_image']) }}', 'حذف صورة البانر؟')"
                            class="bg-red-600 hover:bg-red-700 text-white text-xs px-2 py-1 rounded shadow">
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    </div>
                </div>
                @endif
                <input type="file" name="banner_image" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                <p class="text-xs text-gray-400 mt-1">{{ $post->banner_image ? 'رفع صورة جديدة يستبدل الحالية — Upload to replace' : 'Displayed full-width at top of article' }}</p>
            </div>

            {{-- Gallery Images --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <label class="font-medium text-gray-700">
                        <i class="fas fa-images text-purple-500 ml-1"></i>
                        صور المعرض <span class="en-hint">Gallery / Screenshots</span>
                    </label>
                </div>

                {{-- Existing gallery images with individual delete --}}
                @if($post->gallery_images && count($post->gallery_images))
                <div class="grid grid-cols-3 gap-2 mb-3">
                    @foreach($post->gallery_images as $i => $img)
                    <div class="relative group">
                        <img src="{{ asset('storage/'.$img) }}"
                            class="w-full aspect-video object-cover rounded" loading="lazy">
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition bg-black/40 rounded">
                            <button type="button"
                                onclick="deletePostImage('{{ route('admin.posts.gallery.remove', [$post, $i]) }}', 'حذف هذه الصورة؟')"
                                class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1.5 rounded shadow font-bold">
                                <i class="fas fa-trash ml-1"></i> حذف
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-gray-400 mb-3">لا توجد صور في المعرض — No gallery images yet</p>
                @endif

                {{-- Add more images — JS fetch, no nested form needed --}}
                <div class="border-t pt-3">
                    <label class="text-xs font-medium text-gray-600 mb-1 block">
                        إضافة صور جديدة <span class="en-hint">Add more images (appends to existing)</span>
                    </label>
                    <input type="file" name="gallery_images[]" accept="image/*" multiple
                        class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 mb-2">
                    <p class="text-xs text-gray-400 mb-2">اختر الصور ثم اضغط "حفظ التغييرات" أعلاه — Select images then click Save</p>
                    <button type="submit"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold py-2 rounded-lg transition">
                        <i class="fas fa-upload ml-1"></i> حفظ مع الصور — Save with Images
                    </button>
                </div>
            </div>

            {{-- Tags --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-3">الوسوم <span class="en-hint">Tags (comma-separated)</span></label>
                <input type="text" name="tags"
                    value="{{ old('tags', $post->tags->pluck('name')->join(', ')) }}"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="وسم1, وسم2, وسم3">
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
function deleteDownloadLink(id) {
    if (!confirm('حذف هذا الرابط؟')) return;
    const token = document.querySelector('meta[name=csrf-token]').content;
    fetch('/admin/download-links/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '_token=' + encodeURIComponent(token),
    }).then(() => window.location.reload())
      .catch(() => alert('حدث خطأ'));
}

function deletePostImage(url, msg) {
    if (!confirm(msg)) return;
    const token = document.querySelector('meta[name=csrf-token]').content;
    fetch(url, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '_token=' + encodeURIComponent(token),
    }).then(() => window.location.reload())
      .catch(() => alert('حدث خطأ'));
}

function addGalleryImages(url) {
    const input = document.getElementById('gallery-add-input');
    if (!input.files.length) { alert('اختر صورة واحدة على الأقل'); return; }
    const btn = document.getElementById('gallery-add-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-3 h-3 border border-white border-t-transparent rounded-full animate-spin mr-1"></span> جاري الرفع...';
    const token = document.querySelector('meta[name=csrf-token]').content;
    const formData = new FormData();
    formData.append('_token', token);
    for (const file of input.files) formData.append('images[]', file);
    fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': token }, body: formData })
        .then(r => {
            if (r.ok || r.redirected) { window.location.reload(); }
            else { alert('حدث خطأ في رفع الصور'); btn.disabled = false; btn.innerHTML = '<i class="fas fa-upload ml-1"></i> رفع الصور — Upload Images'; }
        })
        .catch(() => { alert('حدث خطأ في رفع الصور'); btn.disabled = false; btn.innerHTML = '<i class="fas fa-upload ml-1"></i> رفع الصور — Upload Images'; });
}

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
