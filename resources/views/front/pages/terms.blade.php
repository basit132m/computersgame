@extends('layouts.app')
@section('title', 'شروط الاستخدام')
@section('meta_description', 'شروط الاستخدام لموقع ألعاب الكمبيوتر - الرجاء قراءتها قبل استخدام الموقع')

@section('content')
<div class="max-w-4xl mx-auto">
    <article class="bg-white rounded-2xl shadow-sm p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">شروط الاستخدام</h1>
        <p class="text-gray-500 text-sm mb-8">آخر تحديث: {{ date('Y/m/d') }}</p>

        <div class="prose prose-lg max-w-none text-gray-700 space-y-6">
            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3">1. القبول بالشروط</h2>
                <p>باستخدامك لموقع ألعاب الكمبيوتر، فإنك توافق على هذه الشروط والأحكام. إذا كنت لا توافق على هذه الشروط، يُرجى عدم استخدام الموقع.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3">2. حقوق الملكية الفكرية</h2>
                <p>جميع البرامج والألعاب المقدمة على الموقع هي ملك لأصحابها الأصليين. نحن نوفر فقط معلومات ومقالات وروابط تحميل للبرامج المتاحة مجاناً. نحن لا ندعم انتهاك حقوق الملكية الفكرية.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3">3. الاستخدام المقبول</h2>
                <p>يحق لك استخدام الموقع لأغراض شخصية وغير تجارية فقط. لا يحق لك:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>استخدام الموقع لأغراض غير قانونية</li>
                    <li>نسخ أو توزيع محتوى الموقع دون إذن</li>
                    <li>محاولة اختراق أو تعطيل الموقع</li>
                    <li>إرسال محتوى مسيء أو غير لائق في التعليقات</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3">4. إخلاء المسؤولية</h2>
                <p>يُقدَّم الموقع "كما هو" دون أي ضمانات. نحن لسنا مسؤولين عن:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>أي أضرار تنتج عن استخدام البرامج أو الألعاب المُحملة</li>
                    <li>انقطاع خدمة الموقع</li>
                    <li>محتوى المواقع الخارجية المرتبطة بالموقع</li>
                    <li>أي فيروسات أو برمجيات ضارة في روابط التحميل</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3">5. التعليقات</h2>
                <p>عند نشر تعليق، أنت توافق على عدم نشر محتوى مسيء أو إعلاني أو ينتهك حقوق الآخرين. نحتفظ بحق حذف أي تعليق يخالف هذه الشروط.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3">6. التعديلات</h2>
                <p>نحتفظ بحق تعديل هذه الشروط في أي وقت. استمرارك في استخدام الموقع بعد التعديل يعني موافقتك على الشروط الجديدة.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3">7. القانون المطبق</h2>
                <p>تخضع هذه الشروط لأحكام القانون المعمول به. يمكنك <a href="{{ route('contact') }}" class="text-blue-700 hover:underline">التواصل معنا</a> لأي استفسار.</p>
            </section>
        </div>
    </article>
</div>
@endsection
