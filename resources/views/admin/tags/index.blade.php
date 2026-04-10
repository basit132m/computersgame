@extends('layouts.admin')
@section('title', 'إدارة الوسوم')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Add Tag Form --}}
    <div class="bg-white rounded-xl p-5 shadow-sm">
        <h3 class="font-bold text-gray-800 mb-4">إضافة وسم جديد</h3>
        <form action="{{ route('admin.tags.store') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="اسم الوسم"
                class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 mb-3">
            <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-4 rounded-xl transition text-sm">
                إضافة
            </button>
        </form>
    </div>

    {{-- Tags Table --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">الوسم</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">Slug</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">المقالات</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($tags as $tag)
                <tr class="hover:bg-gray-50" x-data="{ editing: false, name: '{{ $tag->name }}' }">
                    <td class="px-4 py-3">
                        <span x-show="!editing" class="font-medium text-gray-800">{{ $tag->name }}</span>
                        <form x-show="editing" action="{{ route('admin.tags.update', $tag) }}" method="POST" class="flex gap-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" x-model="name"
                                class="flex-1 px-2 py-1 border rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="submit" class="text-green-600 text-xs font-medium">حفظ</button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-gray-400 font-mono text-xs hidden md:table-cell">{{ $tag->slug }}</td>
                    <td class="px-4 py-3">
                        <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">{{ $tag->posts_count }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <button @click="editing = !editing" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50 transition">تعديل</button>
                            <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('حذف هذا الوسم؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50 transition">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">لا توجد وسوم بعد</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">{{ $tags->links() }}</div>
    </div>
</div>
@endsection
