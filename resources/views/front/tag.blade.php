@extends('layouts.app')
@section('title', 'وسم: ' . $tag->name)
@section('robots', 'noindex, follow')

@section('content')
<div class="flex gap-6">
    <div class="flex-1 min-w-0">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">🏷️ {{ $tag->name }}</h1>
        @if($posts->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @foreach($posts as $post)
            <x-front.post-card :post="$post" />
            @endforeach
        </div>
        {{ $posts->links('vendor.pagination.arabic') }}
        @else
        <p class="text-gray-500">لا توجد مقالات بهذا الوسم</p>
        @endif
    </div>
    <div class="hidden lg:block w-80 flex-shrink-0" style="position:sticky;bottom:1rem;">
        <x-front.sidebar :trending="$sidebarTrending" :tags="[]" />
    </div>
</div>
@endsection
