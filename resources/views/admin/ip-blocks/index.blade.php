@extends('layouts.admin')
@section('title', 'حظر عناوين IP')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Add IP Form --}}
    <div class="bg-white rounded-xl p-5 shadow-sm">
        <h3 class="font-bold text-gray-800 mb-4">حظر عنوان IP جديد</h3>
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-sm mb-3">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
        @endif
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded-lg text-sm mb-3">{{ session('success') }}</div>
        @endif
        <form action="{{ route('admin.ip-blocks.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">عنوان IP <span class="text-red-500">*</span></label>
                <input type="text" name="ip_address" value="{{ old('ip_address') }}" required
                    class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                    placeholder="192.168.1.1">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">سبب الحظر</label>
                <input type="text" name="reason" value="{{ old('reason') }}"
                    class="w-full px-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="سبب الحظر (اختياري)">
            </div>
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-xl transition text-sm">
                حظر IP
            </button>
        </form>
    </div>

    {{-- Blocked IPs Table --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b bg-gray-50">
            <h3 class="font-bold text-gray-800">عناوين IP المحظورة ({{ $ips->total() }})</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">عنوان IP</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">السبب</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600 hidden lg:table-cell">تاريخ الحظر</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($ips as $ip)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="font-mono text-gray-800">{{ $ip->ip_address }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $ip->reason ?: '—' }}</td>
                    <td class="px-4 py-3 text-gray-400 hidden lg:table-cell text-xs">
                        {{ \Carbon\Carbon::parse($ip->blocked_at)->format('Y/m/d H:i') }}
                    </td>
                    <td class="px-4 py-3">
                        <form action="{{ route('admin.ip-blocks.destroy', $ip) }}" method="POST"
                            onsubmit="return confirm('إلغاء حظر هذا الـ IP؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50 transition">
                                إلغاء الحظر
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">لا توجد عناوين IP محظورة</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">{{ $ips->links() }}</div>
    </div>
</div>
@endsection
