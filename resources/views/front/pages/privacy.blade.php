@extends('layouts.app')
@section('title', 'سياسة الخصوصية')
@section('meta_description', 'سياسة الخصوصية لموقع ألعاب الكمبيوتر - كيف نحمي بياناتك ونستخدمها')

@section('content')
<div class="max-w-4xl mx-auto">
    <article class="bg-white rounded-2xl shadow-sm p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">سياسة الخصوصية</h1>
        <p class="text-gray-500 text-sm mb-8">آخر تحديث: {{ date('Y/m/d') }}</p>

        <div class="prose prose-lg max-w-none text-gray-700 space-y-6">
            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3">1. المعلومات التي نجمعها</h2>
                <p>عند استخدامك لموقع ألعاب الكمبيوتر، قد نجمع المعلومات التالية:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>عنوان IP الخاص بك</li>
                    <li>نوع المتصفح ونظام التشغيل</li>
                    <li>الصفحات التي تزورها على موقعنا</li>
                    <li>الاسم والبريد الإلكتروني عند إرسال تعليق أو رسالة تواصل</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3">2. كيف نستخدم المعلومات</h2>
                <p>نستخدم المعلومات المجموعة من أجل:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>تحسين تجربة المستخدم على الموقع</li>
                    <li>الرد على استفساراتك وتعليقاتك</li>
                    <li>تحليل إحصائيات الموقع لتحسين المحتوى</li>
                    <li>منع إساءة الاستخدام والتصدي للطلبات المشبوهة</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3">3. ملفات تعريف الارتباط (Cookies)</h2>
                <p>يستخدم موقعنا ملفات تعريف الارتباط لتحسين تجربتك. يمكنك التحكم في ملفات تعريف الارتباط من إعدادات متصفحك.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3">4. Google AdSense</h2>
                <p>يستخدم موقعنا Google AdSense لعرض الإعلانات. قد تستخدم Google ملفات تعريف الارتباط لعرض إعلانات بناءً على زياراتك السابقة. يمكنك إلغاء الاشتراك عبر إعدادات الإعلانات في Google.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3">5. أمان المعلومات</h2>
                <p>نحرص على حماية معلوماتك الشخصية باستخدام أحدث تقنيات الأمان. لكننا لا نضمن الأمان الكامل لأي بيانات تُرسل عبر الإنترنت.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3">6. روابط الطرف الثالث</h2>
                <p>قد يحتوي موقعنا على روابط لمواقع خارجية. نحن غير مسؤولين عن سياسات الخصوصية لهذه المواقع.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3">7. التواصل معنا</h2>
                <p>إذا كان لديك أي استفسار حول سياسة الخصوصية، يمكنك <a href="{{ route('contact') }}" class="text-blue-700 hover:underline">التواصل معنا</a>.</p>
            </section>
        </div>
    </article>
</div>
@endsection
