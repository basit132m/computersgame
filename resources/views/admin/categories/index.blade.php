@extends('layouts.admin')
@section('title', 'إدارة التصنيفات')

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.categories.create') }}" class="bg-blue-700 text-white px-5 py-2.5 rounded-lg hover:bg-blue-800 transition font-medium flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة تصنيف
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">التصنيف</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">التصنيف الرئيسي</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">عدد المقالات</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden lg:table-cell">الترتيب</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">إجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($categories as $category)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        @if($category->image)
                        <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" class="w-10 h-10 object-cover rounded-lg">
                        @else
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-800">{{ $category->name }}</p>
                            <p class="text-xs text-gray-400 font-mono">/category/{{ $category->slug }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-500 hidden md:table-cell text-sm">{{ $category->parent?->name ?? '—' }}</td>
                <td class="px-4 py-3 hidden md:table-cell">
                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">{{ $category->posts_count }}</span>
                </td>
                <td class="px-4 py-3 text-gray-500 hidden lg:table-cell text-sm">{{ $category->sort_order }}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50 transition">تعديل</a>
                        <a href="{{ route('category.show', $category->slug) }}" target="_blank" class="text-gray-400 hover:text-gray-600 text-xs px-2 py-1 rounded hover:bg-gray-50 transition">عرض</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                            onsubmit="return confirm('حذف هذا التصنيف؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50 transition">حذف</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">لا توجد تصنيفات بعد</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
