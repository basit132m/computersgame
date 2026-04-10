@extends('layouts.admin')
@section('title', 'إدارة التحويلات')

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.redirects.create') }}" class="bg-blue-700 text-white px-5 py-2.5 rounded-lg hover:bg-blue-800 transition font-medium flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة تحويل
    </a>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm mb-4">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">المسار القديم (من)</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">المسار الجديد (إلى)</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">النوع</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden lg:table-cell">الحالة</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">إجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($redirects as $redirect)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <span class="font-mono text-gray-700 text-xs">{{ $redirect->from_url }}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="font-mono text-blue-600 text-xs">{{ $redirect->to_url }}</span>
                </td>
                <td class="px-4 py-3 hidden md:table-cell">
                    <span class="px-2 py-0.5 rounded text-xs font-medium
                        {{ $redirect->type == 301 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $redirect->type }}
                    </span>
                </td>
                <td class="px-4 py-3 hidden lg:table-cell">
                    <span class="px-2 py-0.5 rounded-full text-xs {{ $redirect->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $redirect->active ? 'مفعّل' : 'معطّل' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.redirects.edit', $redirect) }}" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50 transition">تعديل</a>
                        <form action="{{ route('admin.redirects.destroy', $redirect) }}" method="POST"
                            onsubmit="return confirm('حذف هذا التحويل؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50 transition">حذف</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">لا توجد تحويلات بعد</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">{{ $redirects->links() }}</div>
</div>
@endsection
