@extends('layouts.admin')
@section('title', 'إدارة المستخدمين')

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.users.create') }}" class="bg-blue-700 text-white px-5 py-2.5 rounded-lg hover:bg-blue-800 transition font-medium flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة مستخدم
    </a>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm mb-4">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">المستخدم</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">البريد الإلكتروني</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">الدور</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden lg:table-cell">الحالة</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden lg:table-cell">آخر دخول</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">إجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-600 text-sm">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                        <span class="font-medium text-gray-800">{{ $user->name }}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-500 hidden md:table-cell font-mono text-xs">{{ $user->email }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $user->role === 'admin' ? 'مدير' : 'محرر' }}
                    </span>
                </td>
                <td class="px-4 py-3 hidden lg:table-cell">
                    <span class="px-2 py-0.5 rounded-full text-xs {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $user->status === 'active' ? 'نشط' : 'معطّل' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-400 hidden lg:table-cell text-xs">
                    {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'لم يسجّل دخولاً' }}
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50 transition">تعديل</a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                            onsubmit="return confirm('حذف هذا المستخدم نهائياً؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50 transition">حذف</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">لا يوجد مستخدمون</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
