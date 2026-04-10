@extends('layouts.admin')
@section('title', 'لوحة التحكم')

@section('content')
{{-- Livewire auto-refreshing stats --}}
@livewire('dashboard-stats')

{{-- Recent Comments --}}
<div class="mt-6 bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            أحدث التعليقات
        </h2>
        <a href="{{ route('admin.comments.index') }}" class="text-sm text-blue-600 hover:underline">عرض الكل</a>
    </div>
    @php $recentComments = \App\Models\Comment::with('post')->latest()->limit(5)->get(); @endphp
    @if($recentComments->count())
    <div class="space-y-3">
        @foreach($recentComments as $comment)
        <div class="flex items-start gap-3 border-b pb-3 last:border-b-0">
            <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-bold text-sm flex-shrink-0">
                {{ mb_substr($comment->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <p class="font-medium text-sm text-gray-800">{{ $comment->name }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $comment->status === 'approved' ? 'bg-green-100 text-green-700' : ($comment->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ match($comment->status) { 'approved' => 'موافق', 'rejected' => 'مرفوض', default => 'معلق' } }}
                    </span>
                    <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-xs text-gray-600 line-clamp-1 mt-0.5">{{ $comment->body }}</p>
                @if($comment->post)
                <p class="text-xs text-gray-400">على: {{ $comment->post->title }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-gray-400 text-sm">لا توجد تعليقات بعد</p>
    @endif
</div>
@endsection
