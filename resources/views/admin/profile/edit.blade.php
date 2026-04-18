@extends('layouts.admin')
@section('title', 'حسابي — My Profile')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Account Info --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
            <div>
                <h2 class="font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-xs text-gray-500">{{ $user->role === 'admin' ? 'مدير — Admin' : 'محرر — Editor' }} · {{ $user->email }}</p>
            </div>
        </div>

        <form action="{{ route('admin.profile.update') }}" method="POST" class="p-6 space-y-5">
            @csrf @method('PUT')

            @if(session('success'))
            <div class="flash-success"><i class="fas fa-check-circle ml-2"></i>{{ session('success') }}</div>
            @endif

            @if($errors->any())
            <div class="flash-error">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    الاسم <span class="text-gray-400 font-normal">Username / Display Name</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    البريد الإلكتروني <span class="text-gray-400 font-normal">Email (used for login)</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg transition text-sm">
                <i class="fas fa-save ml-1"></i> حفظ البيانات
            </button>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-lock text-orange-500 text-sm"></i>
                تغيير كلمة المرور
                <span class="text-xs font-normal text-gray-400">Change Password</span>
            </h3>
        </div>

        <form action="{{ route('admin.profile.update') }}" method="POST" class="p-6 space-y-5">
            @csrf @method('PUT')

            {{-- Keep name/email unchanged when only changing password --}}
            <input type="hidden" name="name" value="{{ $user->name }}">
            <input type="hidden" name="email" value="{{ $user->email }}">

            {{-- Current Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    كلمة المرور الحالية <span class="text-gray-400 font-normal">Current Password</span>
                </label>
                <input type="password" name="current_password" autocomplete="current-password"
                    class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-400 @enderror">
                @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- New Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    كلمة المرور الجديدة <span class="text-gray-400 font-normal">New Password (min 8 chars)</span>
                </label>
                <input type="password" name="new_password" autocomplete="new-password"
                    class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('new_password') border-red-400 @enderror">
                @error('new_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Confirm New Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    تأكيد كلمة المرور الجديدة <span class="text-gray-400 font-normal">Confirm New Password</span>
                </label>
                <input type="password" name="new_password_confirmation" autocomplete="new-password"
                    class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 px-6 rounded-lg transition text-sm">
                <i class="fas fa-key ml-1"></i> تغيير كلمة المرور
            </button>
        </form>
    </div>

</div>
@endsection
