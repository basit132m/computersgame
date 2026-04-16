@extends('layouts.admin')
@section('title', 'إضافة مقال جديد')

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
@endsection

@section('content')
<form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" x-data="postForm()">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Title --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-2">عنوان المقال <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full px-4 py-3 border rounded-xl text-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="عنوان واضح وجذاب">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Excerpt --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <label class="font-medium text-gray-700">المقتطف (وصف مختصر)</label>
                    <button type="button" @click="generateAI('excerpt')" class="text-xs text-purple-600 hover:text-purple-700 flex items-center gap-1">
                        <span x-show="!loading.excerpt">✨ توليد تلقائي</span>
                        <span x-show="loading.excerpt">جاري التوليد...</span>
                    </button>
                </div>
                <textarea name="excerpt" id="excerpt" rows="3"
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                    placeholder="وصف مختصر للمقال (يظهر في نتائج البحث)...">{{ old('excerpt') }}</textarea>
            </div>

            {{-- Content --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-2">المحتوى الكامل</label>
                <textarea name="content" id="content">{{ old('content') }}</textarea>
            </div>

            {{-- Game Info --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">معلومات اللعبة / البرنامج</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">الإصدار</label>
                        <input type="text" name="version" value="{{ old('version') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="مثل: 1.0.0">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">المطور</label>
                        <input type="text" name="developer" value="{{ old('developer') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="اسم المطور">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">حجم الملف</label>
                        <input type="text" name="file_size" value="{{ old('file_size') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="مثل: 2.5 GB">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">المنصة</label>
                        <select name="platform" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pc">كمبيوتر</option>
                            <option value="android">أندرويد</option>
                            <option value="ios">iOS</option>
                            <option value="mac">ماك</option>
                            <option value="all">جميع المنصات</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">تاريخ الإصدار</label>
                        <input type="date" name="release_date" value="{{ old('release_date') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">رابط يوتيوب</label>
                        <input type="url" name="youtube_url" value="{{ old('youtube_url') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="https://youtube.com/...">
                    </div>
                </div>
            </div>

            {{-- Detailed Sections --}}
            <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
                <h3 class="font-bold text-gray-800">أقسام تفصيلية</h3>
                <div>
                    <label class="text-sm font-medium text-gray-600 mb-1 block">المميزات</label>
                    <textarea name="features" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="اكتب كل ميزة في سطر منفصل...">{{ old('features') }}</textarea>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600 mb-1 block">متطلبات التشغيل</label>
                    <textarea name="system_requirements" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="المعالج، الذاكرة، الرسوميات...">{{ old('system_requirements') }}</textarea>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600 mb-1 block">ما الجديد</label>
                    <textarea name="whats_new" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="مستجدات هذا الإصدار...">{{ old('whats_new') }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">المميزات (إيجابيات)</label>
                        <textarea name="pros" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="كل ميزة في سطر...">{{ old('pros') }}</textarea>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">العيوب (سلبيات)</label>
                        <textarea name="cons" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="كل عيب في سطر...">{{ old('cons') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SEO Fields --}}
            <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
                <h3 class="font-bold text-gray-800">إعدادات SEO</h3>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-sm font-medium text-gray-600">عنوان الـ Meta (max 60)</label>
                        <button type="button" @click="generateAI('meta_title')" class="text-xs text-purple-600 hover:text-purple-700">
                            <span x-show="!loading.meta_title">✨ توليد تلقائي</span>
                            <span x-show="loading.meta_title">جاري التوليد...</span>
                        </button>
                    </div>
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="عنوان محسّن لمحركات البحث">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-sm font-medium text-gray-600">وصف الـ Meta (max 155)</label>
                        <button type="button" @click="generateAI('meta_description')" class="text-xs text-purple-600 hover:text-purple-700">
                            <span x-show="!loading.meta_description">✨ توليد تلقائي</span>
                            <span x-show="loading.meta_description">جاري التوليد...</span>
                        </button>
                    </div>
                    <textarea name="meta_description" id="meta_description" rows="2"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="وصف محسّن يظهر في نتائج البحث...">{{ old('meta_description') }}</textarea>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-sm font-medium text-gray-600">الكلمات المفتاحية</label>
                        <button type="button" @click="generateAI('meta_keywords')" class="text-xs text-purple-600 hover:text-purple-700">
                            <span x-show="!loading.meta_keywords">✨ توليد تلقائي</span>
                            <span x-show="loading.meta_keywords">جاري التوليد...</span>
                        </button>
                    </div>
                    <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords') }}"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="كلمة1, كلمة2, كلمة3...">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600 mb-1 block">Robots</label>
                    <input type="text" name="robots" value="{{ old('robots', 'index, follow') }}"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600 mb-1 block">Schema Type</label>
                    <select name="schema_type" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="SoftwareApplication">SoftwareApplication</option>
                        <option value="Article">Article</option>
                        <option value="Review">Review</option>
                        <option value="HowTo">HowTo</option>
                        <option value="ItemList">ItemList</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            {{-- Publish Controls --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">النشر</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">الحالة</label>
                        <select name="status" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="draft">مسودة</option>
                            <option value="pending">قيد المراجعة</option>
                            <option value="published">نشر الآن</option>
                            <option value="scheduled">جدولة</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">النوع</label>
                        <select name="type" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="game">لعبة</option>
                            <option value="software">برنامج</option>
                            <option value="apk">تطبيق أندرويد</option>
                            <option value="blog">مقال</option>
                            <option value="tutorial">شرح</option>
                            <option value="listicle">قائمة</option>
                            <option value="review">مراجعة</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">Slug (الرابط)</label>
                        <input type="text" name="slug" value="{{ old('slug') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="سيتم توليده تلقائياً">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-1 block">تاريخ النشر</label>
                        <input type="datetime-local" name="published_at" value="{{ old('published_at') }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-4 rounded-lg transition text-sm">
                        حفظ
                    </button>
                    <a href="{{ route('admin.posts.index') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-4 rounded-lg transition text-sm">
                        إلغاء
                    </a>
                </div>
            </div>

            {{-- Category --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-3">التصنيف</label>
                <select name="category_id" class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- اختر تصنيفاً --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->parent ? '↳ ' : '' }}{{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Featured Image --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-3">الصورة الرئيسية</label>
                <input type="file" name="featured_image" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-400 mt-2">سيتم التحويل تلقائياً إلى WebP</p>
            </div>

            {{-- Tags --}}
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <label class="block font-medium text-gray-700 mb-3">الوسوم</label>
                <input type="text" name="tags" value="{{ old('tags') }}"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="أدخل الوسوم مفصولة بفواصل">
                <p class="text-xs text-gray-400 mt-1">مثل: لعبة, مجانية, PC</p>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
function postForm() {
    return {
        loading: {
            meta_title: false,
            meta_description: false,
            meta_keywords: false,
            excerpt: false,
        },
        async generateAI(action) {
            this.loading[action] = true;
            const title = document.querySelector('[name=title]')?.value || '';
            const category = document.querySelector('[name=category_id] option:checked')?.text || '';
            const type = document.querySelector('[name=type]')?.value || '';
            const content = tinymce.get('content')?.getContent({ format: 'text' }) || '';

            try {
                const res = await fetch('/admin/ai/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                    },
                    body: JSON.stringify({ action, title, category, type, content })
                });
                const data = await res.json();
                if (data.success) {
                    const el = document.getElementById(action);
                    if (el) el.value = data.result;
                } else {
                    alert('حدث خطأ، يرجى المحاولة مرة أخرى');
                }
            } catch {
                alert('حدث خطأ، يرجى المحاولة مرة أخرى');
            } finally {
                this.loading[action] = false;
            }
        }
    }
}
</script>
@endsection
