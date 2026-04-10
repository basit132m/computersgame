@extends('layouts.admin')
@section('title', 'إدارة المقالات')

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.posts.create') }}" class="bg-blue-700 text-white px-5 py-2.5 rounded-lg hover:bg-blue-800 transition font-medium flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        إضافة مقال جديد
    </a>
</div>

{{-- Livewire PostsTable with live search and column sorting --}}
@livewire('posts-table')
@endsection
