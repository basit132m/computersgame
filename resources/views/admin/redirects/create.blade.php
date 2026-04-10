@extends('layouts.admin')
@section('title', 'إضافة تحويل')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.redirects.store') }}" method="POST" class="space-y-4">
        @csrf
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
        @endif

        <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">المسار القديم (من) <span class="text-red-500">*</span></label>
                <input type="text" name="from_url" value="{{ old('from_url') }}" required
                    class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                    placeholder="/old-page-url">
                <p class="text-xs text-gray-400 mt-1">يجب أن يبدأ بـ / — مثال: /old-game-name</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">المسار الجديد (إلى) <span class="text-red-500">*</span></label>
                <input type="text" name="to_url" value="{{ old('to_url') }}" required
                    class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                    placeholder="/new-game-name أو https://external.com/page">
                <p class="text-xs text-gray-400 mt-1">مسار داخلي يبدأ بـ / أو رابط خارجي كامل</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">نوع التحويل</label>
                    <select name="type" class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="301" {{ old('type') === '301' ? 'selected' : '' }}>301 — دائم (Permanent)</option>
                        <option value="302" {{ old('type') === '302' ? 'selected' : '' }}>302 — مؤقت (Temporary)</option>
                    </select>
                </div>
                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">مفعّل</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-8 rounded-xl transition">حفظ التحويل</button>
            <a href="{{ route('admin.redirects.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-8 rounded-xl transition">إلغاء</a>
        </div>
    </form>
</div>
@endsection
