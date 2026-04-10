@extends('layouts.admin')
@section('title', 'تعديل: ' . $category->name)

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf @method('PUT')

        <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">اسم التصنيف <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $category->slug) }}"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">التصنيف الرئيسي</label>
                <select name="parent_id" class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— بدون تصنيف رئيسي —</option>
                    @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $category->description) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">عنوان Meta</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ترتيب العرض</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وصف Meta</label>
                <textarea name="meta_description" rows="2"
                    class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('meta_description', $category->meta_description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">صورة التصنيف</label>
                @if($category->image)
                <img src="{{ asset('storage/'.$category->image) }}" class="w-24 h-24 object-cover rounded-lg mb-2">
                @endif
                <input type="file" name="image" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700">
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-8 rounded-xl transition">حفظ التغييرات</button>
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-8 rounded-xl transition">إلغاء</a>
        </div>
    </form>
</div>
@endsection
