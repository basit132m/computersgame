@extends('layouts.admin')
@section('title', 'بانرات الشريط الجانبي')

@section('content')
<div class="space-y-6">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flash-success"><i class="fas fa-check-circle ml-2"></i>{{ session('success') }}</div>
    @endif

    {{-- ═══════════════════════
         ADD NEW BANNER
    ═══════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-plus-circle text-blue-600 text-sm"></i>
                إضافة بانر جديد
                <span class="text-xs font-normal text-gray-400">Add New Banner — 341×179px recommended</span>
            </h3>
        </div>
        <form action="{{ route('admin.sidebar-banners.store') }}" method="POST" enctype="multipart/form-data" class="p-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الصورة <span class="text-red-500">*</span> <span class="text-xs text-gray-400">341×179px</span></label>
                    <input type="file" name="image" accept="image/*" required
                        class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">رابط المقال <span class="text-red-500">*</span> <span class="text-xs text-gray-400">Article URL</span></label>
                    <input type="url" name="url" placeholder="https://computersgame.org/slug" required
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('url') border-red-400 @enderror">
                    @error('url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">العنوان <span class="text-xs text-gray-400">Title overlay (optional)</span></label>
                    <input type="text" name="title" placeholder="عنوان المقال بالعربية"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg transition text-sm">
                    <i class="fas fa-plus ml-1"></i> إضافة البانر
                </button>
            </div>
        </form>
    </div>

    {{-- ═══════════════════════
         BANNERS LIST
    ═══════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-images text-green-600 text-sm"></i>
                البانرات الحالية
                <span class="bg-gray-200 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $banners->count() }}</span>
            </h3>
        </div>

        @if($banners->isEmpty())
        <div class="py-12 text-center text-gray-400">
            <i class="fas fa-image text-4xl mb-3 block text-gray-300"></i>
            <p>لم يتم إضافة أي بانرات بعد</p>
        </div>
        @else
        <div class="divide-y divide-gray-100">
            @foreach($banners as $banner)
            <div class="p-4 flex items-start gap-4" x-data="{ editing: false }">

                {{-- Thumbnail --}}
                <div class="flex-shrink-0">
                    <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title }}"
                        class="w-32 rounded-lg border border-gray-200 object-cover" style="aspect-ratio:341/179;">
                </div>

                {{-- View mode --}}
                <div class="flex-1 min-w-0" x-show="!editing">
                    <p class="font-semibold text-gray-800 text-sm mb-1">{{ $banner->title ?: '(بدون عنوان)' }}</p>
                    <a href="{{ $banner->url }}" target="_blank" class="text-xs text-blue-600 hover:underline truncate block">{{ $banner->url }}</a>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $banner->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $banner->active ? 'مفعّل' : 'معطّل' }}
                        </span>
                        <span class="text-xs text-gray-400">الترتيب: {{ $banner->sort_order }}</span>
                    </div>
                    <div class="flex items-center gap-2 mt-3">
                        <button @click="editing = true"
                            class="text-xs bg-yellow-50 border border-yellow-300 text-yellow-700 hover:bg-yellow-100 px-3 py-1.5 rounded-lg transition font-medium">
                            <i class="fas fa-edit ml-1"></i> تعديل
                        </button>
                        <form action="{{ route('admin.sidebar-banners.destroy', $banner) }}" method="POST"
                              onsubmit="return confirm('حذف هذا البانر نهائياً؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="text-xs bg-red-50 border border-red-300 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg transition font-medium">
                                <i class="fas fa-trash ml-1"></i> حذف
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Edit mode --}}
                <form action="{{ route('admin.sidebar-banners.update', $banner) }}" method="POST"
                      enctype="multipart/form-data" class="flex-1 min-w-0 space-y-3" x-show="editing">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">رابط المقال</label>
                            <input type="url" name="url" value="{{ $banner->url }}" required
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">العنوان</label>
                            <input type="text" name="title" value="{{ $banner->title }}"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">صورة جديدة (اختياري)</label>
                            <input type="file" name="image" accept="image/*"
                                class="w-full text-sm text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">الترتيب</label>
                            <input type="number" name="sort_order" value="{{ $banner->sort_order }}" min="0"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="active" value="1" {{ $banner->active ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-blue-600">
                            <span class="text-sm text-gray-700">مفعّل</span>
                        </label>
                        <button type="submit"
                            class="text-xs bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-1.5 rounded-lg transition">
                            <i class="fas fa-save ml-1"></i> حفظ
                        </button>
                        <button type="button" @click="editing = false"
                            class="text-xs text-gray-500 hover:text-gray-700 px-3 py-1.5 rounded-lg border border-gray-300 transition">
                            إلغاء
                        </button>
                    </div>
                </form>

            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection
