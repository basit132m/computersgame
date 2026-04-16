@extends('layouts.admin')
@section('title', 'إضافة تصنيف — Add Category')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
        @endif

        <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">اسم التصنيف <span class="en-hint">Category Name</span> <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="مثل: ألعاب الأكشن">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="en-hint">URL path — leave blank to auto-generate</span></label>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                        placeholder="action-games">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">التصنيف الرئيسي <span class="en-hint">Parent Category (optional)</span></label>
                <select name="parent_id" class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— بدون تصنيف رئيسي —</option>
                    @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الوصف <span class="en-hint">Description</span></label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">عنوان Meta <span class="en-hint">Meta Title</span></label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ترتيب العرض <span class="en-hint">Display Order</span></label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وصف Meta <span class="en-hint">Meta Description</span></label>
                <textarea name="meta_description" rows="2"
                    class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('meta_description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">صورة التصنيف <span class="en-hint">Category Image</span></label>
                <input type="file" name="image" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700">
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-8 rounded-xl transition">حفظ التصنيف</button>
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-8 rounded-xl transition">إلغاء</a>
        </div>
    </form>
</div>
@endsection
