@extends('layouts.admin')
@section('title', 'تعديل مستخدم')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
        @endif

        <div class="bg-white rounded-xl p-5 shadow-sm space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الاسم <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور الجديدة</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="اتركها فارغة لعدم التغيير">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation"
                        class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الدور</label>
                    <select name="role" class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                        <option value="editor" {{ old('role', $user->role) === 'editor' ? 'selected' : '' }}>محرر</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>مدير</option>
                    </select>
                    @if($user->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <p class="text-xs text-gray-400 mt-1">لا يمكن تغيير دورك بنفسك</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>معطّل</option>
                    </select>
                    @if($user->id === auth()->id())
                        <input type="hidden" name="status" value="{{ $user->status }}">
                    @endif
                </div>
            </div>

            {{-- User Stats --}}
            <div class="pt-2 border-t grid grid-cols-3 gap-4 text-center">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-2xl font-bold text-gray-800">{{ $user->posts()->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">مقال</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm font-semibold text-gray-800">{{ $user->created_at->format('Y/m/d') }}</p>
                    <p class="text-xs text-gray-500 mt-1">تاريخ الإنشاء</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm font-semibold text-gray-800">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'لم يسجّل' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">آخر دخول</p>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-8 rounded-xl transition">حفظ التعديلات</button>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-8 rounded-xl transition">إلغاء</a>
        </div>
    </form>
</div>
@endsection
