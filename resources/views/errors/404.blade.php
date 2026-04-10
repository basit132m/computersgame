@extends('layouts.app')
@section('title', 'الصفحة غير موجودة - 404')
@section('robots', 'noindex, nofollow')

@section('content')
<div class="text-center py-20">
    <div class="text-8xl mb-6">🎮</div>
    <h1 class="text-4xl font-bold text-gray-900 mb-4">404 - الصفحة غير موجودة</h1>
    <p class="text-xl text-gray-600 mb-8">عذراً، الصفحة التي تبحث عنها غير موجودة أو تم نقلها</p>
    <div class="flex flex-wrap justify-center gap-4">
        <a href="{{ url('/') }}" class="bg-blue-700 text-white font-bold px-8 py-3 rounded-xl hover:bg-blue-800 transition">
            العودة للرئيسية
        </a>
        <a href="{{ route('search') }}" class="bg-gray-100 text-gray-700 font-bold px-8 py-3 rounded-xl hover:bg-gray-200 transition">
            البحث في الموقع
        </a>
    </div>

    {{-- Suggest categories --}}
    <div class="mt-12">
        <p class="text-gray-500 mb-4">تصفح التصنيفات:</p>
        <div class="flex flex-wrap justify-center gap-3">
            @foreach(\App\Models\Category::whereNull('parent_id')->orderBy('sort_order')->limit(6)->get() as $cat)
            <a href="{{ route('category.show', $cat->slug) }}"
                class="bg-white border border-gray-200 text-gray-700 px-5 py-2 rounded-full hover:border-blue-500 hover:text-blue-700 transition">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
