<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>@yield('title', $globalSettings['site_name'] ?? 'ألعاب الكمبيوتر')</title>
    <meta name="description" content="@yield('meta_description', $globalSettings['site_description'] ?? '')">
    @hasSection('meta_keywords')
    <meta name="keywords" content="@yield('meta_keywords')">
    @endif
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', $globalSettings['site_name'] ?? '')">
    <meta property="og:description" content="@yield('og_description', $globalSettings['site_description'] ?? '')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:site_name" content="{{ $globalSettings['site_name'] ?? '' }}">
    <meta property="og:locale" content="ar_AR">
    @hasSection('og_image')
    <meta property="og:image" content="@yield('og_image')">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/jpeg">
    <meta name="twitter:image" content="@yield('og_image')">
    @elseif(!empty($globalSettings['logo']))
    <meta property="og:image" content="{{ asset('storage/'.$globalSettings['logo']) }}">
    <meta property="og:image:width" content="600">
    <meta property="og:image:height" content="600">
    <meta name="twitter:image" content="{{ asset('storage/'.$globalSettings['logo']) }}">
    @endif
    <meta name="theme-color" content="#30A38A">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', $globalSettings['site_name'] ?? '')">
    <meta name="twitter:description" content="@yield('og_description', $globalSettings['site_description'] ?? '')">

    <link rel="icon" type="image/x-icon" href="{{ ($globalSettings['favicon'] ?? '') ? asset('storage/'.$globalSettings['favicon']) : asset('favicon.ico') }}">

    @yield('pagination_links')

    {{-- DNS prefetch for external resources --}}
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

    {{-- Google Fonts: preconnect + preload to avoid render-blocking --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap"
          onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap"></noscript>

    {{-- Font Awesome 6: load async so it never blocks first paint --}}
    <link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          crossorigin="anonymous" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous"></noscript>

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root { --brand: #30A38A; --brand-dark: #268a74; }
        body { font-family: 'Almarai', 'Tahoma', sans-serif; direction: rtl; text-align: right; }
        .brand-text { color: #30A38A; }
        .brand-bg { background-color: #30A38A; }
        .brand-border { border-color: #30A38A; }
        .cat-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 14px; border: 1.5px solid #30A38A; border-radius: 5px;
            color: #30A38A; font-weight: 700; font-size: 13px;
            white-space: nowrap; transition: all .2s;
        }
        .cat-btn:hover { background: #30A38A; color: #fff; }
        .cat-btn i { font-size: 11px; }
        /* Hide scrollbar but keep scrollable */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        /* Search slide */
        [x-cloak] { display: none !important; }
    </style>

    @yield('schema')

    @if(!empty($globalSettings['analytics_id']))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $globalSettings['analytics_id'] }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $globalSettings['analytics_id'] }}');</script>
    @endif

    @if(!empty($globalSettings['clarity_id']))
    <script>(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y)})(window,document,"clarity","script","{{ $globalSettings['clarity_id'] }}");</script>
    @endif

    @stack('head')
</head>
<body class="bg-gray-100 text-gray-900" x-data="{ searchOpen: false, mobileMenu: false }" x-cloak>

{{-- ═══════════════════════════════════════════
     HEADER
═══════════════════════════════════════════ --}}
<header class="sticky top-0 z-50 shadow-md">

    {{-- Row 1: Black bar — Search | Logo | Social --}}
    <div class="bg-black">
        <div class="max-w-7xl mx-auto px-6 h-24 flex items-center justify-between gap-4">

            {{-- Right: Social Icons --}}
            <div class="flex items-center gap-3">
                @if(!empty($globalSettings['facebook_url']))
                <a href="{{ $globalSettings['facebook_url'] }}" target="_blank" rel="noopener" aria-label="فيسبوك"
                   class="text-gray-400 hover:text-[#30A38A] transition text-base"><i class="fab fa-facebook-f"></i></a>
                @endif
                @if(!empty($globalSettings['twitter_url']))
                <a href="{{ $globalSettings['twitter_url'] }}" target="_blank" rel="noopener" aria-label="تويتر"
                   class="text-gray-400 hover:text-[#30A38A] transition text-base"><i class="fab fa-x-twitter"></i></a>
                @endif
                @if(!empty($globalSettings['telegram_url']))
                <a href="{{ $globalSettings['telegram_url'] }}" target="_blank" rel="noopener" aria-label="تيليغرام"
                   class="text-gray-400 hover:text-[#30A38A] transition text-base"><i class="fab fa-telegram-plane"></i></a>
                @endif
                @if(!empty($globalSettings['youtube_url']))
                <a href="{{ $globalSettings['youtube_url'] }}" target="_blank" rel="noopener" aria-label="يوتيوب"
                   class="text-gray-400 hover:text-[#30A38A] transition text-base"><i class="fab fa-youtube"></i></a>
                @endif
                @if(!empty($globalSettings['whatsapp_number']))
                <a href="https://wa.me/{{ $globalSettings['whatsapp_number'] }}" target="_blank" rel="noopener" aria-label="واتساب"
                   class="text-gray-400 hover:text-[#30A38A] transition text-base"><i class="fab fa-whatsapp"></i></a>
                @endif
            </div>

            {{-- Center: Logo --}}
            <a href="{{ route('home') }}" class="flex-1 flex flex-col items-center justify-center text-center gap-1">
                @if(!empty($globalSettings['logo']))
                    <img src="{{ asset('storage/'.$globalSettings['logo']) }}"
                         alt="{{ $globalSettings['site_name'] ?? 'ألعاب الكمبيوتر' }}"
                         class="h-10 w-auto mx-auto">
                @else
                    <span class="text-2xl font-black text-[#30A38A] leading-tight">{{ $globalSettings['site_name'] ?? 'ألعاب الكمبيوتر' }}</span>
                @endif
                <span class="text-white font-bold text-xs sm:text-sm leading-tight">تحميل العاب كمبيوتر وبرامج وتطبيقات اندرويد مجانا</span>
            </a>

            {{-- Left: Search Toggle --}}
            <button @click="searchOpen = !searchOpen"
                    class="flex items-center gap-2 text-gray-300 hover:text-[#30A38A] transition focus:outline-none"
                    aria-label="بحث">
                <i class="fas fa-search text-lg" x-show="!searchOpen"></i>
                <i class="fas fa-times text-lg" x-show="searchOpen"></i>
            </button>

        </div>
    </div>

    {{-- Slide-down Search Bar --}}
    <div x-show="searchOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-3"
         class="bg-gray-900 border-b border-gray-700 py-3 px-4">
        <form action="{{ route('search') }}" method="GET" class="max-w-2xl mx-auto flex gap-2">
            <button type="submit" class="bg-[#30A38A] hover:bg-[#268a74] text-white px-5 py-2 rounded-lg text-sm font-bold transition flex-shrink-0">
                <i class="fas fa-search ml-1"></i> بحث
            </button>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="ابحث عن لعبة أو برنامج أو تطبيق..." autofocus
                   class="flex-1 px-4 py-2 rounded-lg bg-gray-800 text-white border border-gray-600 focus:outline-none focus:border-[#30A38A] text-sm text-right">
        </form>
    </div>

    {{-- Row 2: Categories nav — White background --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6">

            {{-- Desktop categories --}}
            <div class="hidden md:flex items-center gap-2 py-2 overflow-x-auto scrollbar-hide">
                <a href="{{ route('home') }}" class="cat-btn">
                    <i class="fas fa-home"></i> الرئيسية
                </a>
                @php $catIcons = ['fa-gamepad','fa-car','fa-futbol','fa-motorcycle','fa-fighter-jet','fa-dragon','fa-chess','fa-crosshairs','fa-trophy','fa-dice']; @endphp
                @foreach($navCategories->take(8) as $i => $cat)
                <a href="{{ route('category.show', $cat->slug) }}" class="cat-btn">
                    <i class="fas {{ $catIcons[$i % count($catIcons)] }}"></i>
                    {{ $cat->name }}
                </a>
                @endforeach
            </div>

            {{-- Mobile: hamburger + scrollable cats --}}
            <div class="md:hidden flex items-center gap-2 py-2">
                <button @click="mobileMenu = !mobileMenu"
                        class="flex-shrink-0 p-2 text-[#30A38A] border border-[#30A38A] rounded" aria-label="القائمة">
                    <i class="fas fa-bars text-sm" x-show="!mobileMenu"></i>
                    <i class="fas fa-times text-sm" x-show="mobileMenu"></i>
                </button>
                <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide">
                    <a href="{{ route('home') }}" class="cat-btn"><i class="fas fa-home"></i> الرئيسية</a>
                    @foreach($navCategories as $i => $cat)
                    <a href="{{ route('category.show', $cat->slug) }}" class="cat-btn">
                        <i class="fas {{ $catIcons[$i % count($catIcons)] }}"></i> {{ $cat->name }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Mobile dropdown menu --}}
        <div x-show="mobileMenu"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden border-t border-gray-100 bg-white px-4 pb-3">
            <div class="grid grid-cols-2 gap-2 pt-3">
                @foreach($navCategories->take(12) as $i => $cat)
                <a href="{{ route('category.show', $cat->slug) }}"
                   class="flex items-center gap-2 px-3 py-2 rounded border border-gray-200 text-sm font-bold text-[#30A38A] hover:bg-[#30A38A] hover:text-white hover:border-[#30A38A] transition">
                    <i class="fas {{ $catIcons[$i % count($catIcons)] }} text-xs"></i>
                    {{ $cat->name }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

</header>

{{-- Header Ad --}}
@adslot('header_ad')

{{-- Main Content --}}
<main class="max-w-7xl mx-auto px-6 py-6">
    @yield('content')
</main>

{{-- Footer Ad --}}
@adslot('footer_ad')

{{-- ═══════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════ --}}
<footer class="mt-8">

    {{-- Main Footer: Dark like header --}}
    <div class="bg-black text-white">
        <div class="max-w-7xl mx-auto px-6 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">

                {{-- Logo + Description + Social --}}
                <div class="text-center md:text-right">
                    <a href="{{ route('home') }}" class="inline-block mb-3">
                        @if(!empty($globalSettings['logo']))
                            <img src="{{ asset('storage/'.$globalSettings['logo']) }}"
                                 alt="{{ $globalSettings['site_name'] ?? '' }}"
                                 class="h-10 w-auto mx-auto md:mx-0">
                        @else
                            <span class="text-2xl font-black text-[#30A38A]">{{ $globalSettings['site_name'] ?? 'ألعاب الكمبيوتر' }}</span>
                        @endif
                    </a>
                    <p class="text-gray-400 text-sm leading-relaxed mt-2">{{ $globalSettings['site_description'] ?? 'موقع عربي متخصص في تحميل الألعاب والبرامج.' }}</p>
                    <div class="flex items-center justify-center md:justify-start gap-4 mt-4">
                        @if(!empty($globalSettings['facebook_url']))
                        <a href="{{ $globalSettings['facebook_url'] }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-[#30A38A] transition text-lg"><i class="fab fa-facebook-f"></i></a>
                        @endif
                        @if(!empty($globalSettings['twitter_url']))
                        <a href="{{ $globalSettings['twitter_url'] }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-[#30A38A] transition text-lg"><i class="fab fa-x-twitter"></i></a>
                        @endif
                        @if(!empty($globalSettings['telegram_url']))
                        <a href="{{ $globalSettings['telegram_url'] }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-[#30A38A] transition text-lg"><i class="fab fa-telegram-plane"></i></a>
                        @endif
                        @if(!empty($globalSettings['youtube_url']))
                        <a href="{{ $globalSettings['youtube_url'] }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-[#30A38A] transition text-lg"><i class="fab fa-youtube"></i></a>
                        @endif
                        @if(!empty($globalSettings['whatsapp_number']))
                        <a href="https://wa.me/{{ $globalSettings['whatsapp_number'] }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-[#30A38A] transition text-lg"><i class="fab fa-whatsapp"></i></a>
                        @endif
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="font-bold text-[#30A38A] mb-4 text-base flex items-center gap-2">
                        <i class="fas fa-link text-sm"></i> روابط سريعة
                    </h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('home') }}" class="hover:text-[#30A38A] transition flex items-center gap-2"><i class="fas fa-angle-left text-xs"></i> الرئيسية</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-[#30A38A] transition flex items-center gap-2"><i class="fas fa-angle-left text-xs"></i> من نحن</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-[#30A38A] transition flex items-center gap-2"><i class="fas fa-angle-left text-xs"></i> اتصل بنا</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-[#30A38A] transition flex items-center gap-2"><i class="fas fa-angle-left text-xs"></i> سياسة الخصوصية</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-[#30A38A] transition flex items-center gap-2"><i class="fas fa-angle-left text-xs"></i> شروط الاستخدام</a></li>
                    </ul>
                </div>

                {{-- Categories --}}
                <div>
                    <h4 class="font-bold text-[#30A38A] mb-4 text-base flex items-center gap-2">
                        <i class="fas fa-gamepad text-sm"></i> أقسام الموقع
                    </h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        @foreach($navCategories->take(7) as $cat)
                        <li>
                            <a href="{{ route('category.show', $cat->slug) }}"
                               class="hover:text-[#30A38A] transition flex items-center gap-2">
                                <i class="fas fa-angle-left text-xs"></i> {{ $cat->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </div>

    {{-- Copyright Bar --}}
    <div class="bg-gray-950 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-6 py-3 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-500">
            <div class="flex items-center gap-4">
                <a href="{{ route('contact') }}" class="hover:text-[#30A38A] transition">اتصل بنا</a>
                <a href="{{ route('privacy') }}" class="hover:text-[#30A38A] transition">سياسة الخصوصية</a>
                <a href="{{ route('terms') }}" class="hover:text-[#30A38A] transition">شروط الاستخدام</a>
            </div>
            <div>
                {!! $globalSettings['footer_text'] ?? 'جميع الحقوق محفوظة &copy; ' . date('Y') !!}
            </div>
        </div>
    </div>

</footer>

{{-- Scroll to Top Button --}}
<button id="scrollTopBtn"
    onclick="window.scrollTo({top:0,behavior:'smooth'})"
    style="position:fixed;bottom:24px;left:24px;z-index:9999;width:44px;height:44px;border-radius:50%;background:#30A38A;color:#fff;border:none;cursor:pointer;display:none;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.25);"
    aria-label="العودة للأعلى"
    title="العودة للأعلى">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
    </svg>
</button>
<script>
(function(){
    var btn = document.getElementById('scrollTopBtn');
    window.addEventListener('scroll', function(){
        btn.style.display = window.scrollY > 300 ? 'flex' : 'none';
    }, {passive:true});
})();
</script>

@yield('scripts')
@stack('scripts')
</body>
</html>
