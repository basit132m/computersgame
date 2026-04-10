@extends('layouts.app')
@section('title', 'اتصل بنا')
@section('meta_description', 'تواصل معنا في موقع ألعاب الكمبيوتر - نحن هنا للمساعدة')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">اتصل بنا</h1>

    @if(session('contact_success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl mb-6">
        ✅ {{ session('contact_success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm p-8">
        <p class="text-gray-600 mb-6">هل لديك سؤال أو اقتراح أو تريد الإبلاغ عن رابط معطل؟ تواصل معنا وسنرد عليك في أقرب وقت.</p>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('contact.send') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الاسم <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="اسمك الكريم">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="example@email.com">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الموضوع <span class="text-red-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="موضوع رسالتك">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الرسالة <span class="text-red-500">*</span></label>
                <textarea name="message" rows="5" required
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="اكتب رسالتك هنا...">{{ old('message') }}</textarea>
            </div>
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-10 rounded-xl transition">
                إرسال الرسالة
            </button>
        </form>
    </div>

    {{-- Contact Info --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        @if($globalSettings['contact_email'] ?? null)
        <div class="bg-white rounded-xl p-4 text-center shadow-sm">
            <p class="text-2xl mb-2">✉️</p>
            <p class="font-medium text-sm text-gray-700">البريد الإلكتروني</p>
            <p class="text-gray-500 text-sm">{{ Setting::get('contact_email') }}</p>
        </div>
        @endif
        @if($globalSettings['telegram_url'])
        <div class="bg-white rounded-xl p-4 text-center shadow-sm">
            <p class="text-2xl mb-2">📱</p>
            <p class="font-medium text-sm text-gray-700">قناة تيليغرام</p>
            <a href="{{ $globalSettings['telegram_url'] }}" class="text-blue-600 text-sm hover:underline">اشترك الآن</a>
        </div>
        @endif
        @if($globalSettings['whatsapp_number'])
        <div class="bg-white rounded-xl p-4 text-center shadow-sm">
            <p class="text-2xl mb-2">💬</p>
            <p class="font-medium text-sm text-gray-700">واتساب</p>
            <a href="https://wa.me/{{ $globalSettings['whatsapp_number'] }}" class="text-green-600 text-sm hover:underline">تواصل الآن</a>
        </div>
        @endif
    </div>
</div>
@endsection
