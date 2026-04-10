<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {{-- SEO Meta Tags --}}
    <title>@yield('title', $globalSettings['site_name']) | {{ $globalSettings['site_name'] }}</title>
    <meta name="description" content="@yield('meta_description', $globalSettings['site_description'])">
    @hasSection('meta_keywords')
    <meta name="keywords" content="@yield('meta_keywords')">
    @endif
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', $globalSettings['site_name'])">
    <meta property="og:description" content="@yield('og_description', $globalSettings['site_description'])">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:site_name" content="{{ $globalSettings['site_name'] }}">
    <meta property="og:locale" content="ar_AR">
    @hasSection('og_image')
    <meta property="og:image" content="@yield('og_image')">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', $globalSettings['site_name'])">
    <meta name="twitter:description" content="@yield('og_description', $globalSettings['site_description'])">

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ $globalSettings['favicon'] ? asset('storage/'.$globalSettings['favicon']) : asset('favicon.ico') }}">

    {{-- Pagination rel --}}
    @yield('pagination_links')

    {{-- Google Fonts Arabic --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    {{-- Compiled CSS + JS (Alpine.js bundled) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- JSON-LD Schema --}}
    @yield('schema')

    {{-- Google Analytics --}}
    @if($globalSettings['analytics_id'])
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $globalSettings['analytics_id'] }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $globalSettings['analytics_id'] }}');
    </script>
    @endif

    {{-- Microsoft Clarity --}}
    @if($globalSettings['clarity_id'])
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y)})(window, document, "clarity", "script", "{{ $globalSettings['clarity_id'] }}");
    </script>
    @endif

    @yield('head')
</head>
<body class="bg-gray-50 text-gray-900" x-data="{ mobileMenu: false }">

    {{-- Navigation --}}
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0">
                    @if($globalSettings['logo'])
                        <img src="{{ asset('storage/'.$globalSettings['logo']) }}" alt="{{ $globalSettings['site_name'] }}" class="h-10 w-auto">
                    @else
                        <span class="text-2xl font-bold text-blue-700">🎮 {{ $globalSettings['site_name'] }}</span>
                    @endif
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-700 transition">الرئيسية</a>
                    @foreach(\App\Models\Category::whereNull('parent_id')->orderBy('sort_order')->limit(6)->get() as $cat)
                    <a href="{{ route('category.show', $cat->slug) }}" class="text-gray-700 hover:text-blue-700 transition">{{ $cat->name }}</a>
                    @endforeach
                    <a href="{{ route('about') }}" class="text-gray-700 hover:text-blue-700 transition">من نحن</a>
                    <a href="{{ route('contact') }}" class="text-gray-700 hover:text-blue-700 transition">اتصل بنا</a>
                </nav>

                {{-- Search + Mobile Toggle --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('search') }}" class="text-gray-600 hover:text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </a>
                    <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded text-gray-600">
                        <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileMenu" x-transition class="md:hidden border-t py-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded text-gray-700 hover:bg-blue-50">الرئيسية</a>
                @foreach(\App\Models\Category::whereNull('parent_id')->orderBy('sort_order')->limit(8)->get() as $cat)
                <a href="{{ route('category.show', $cat->slug) }}" class="block px-3 py-2 rounded text-gray-700 hover:bg-blue-50">{{ $cat->name }}</a>
                @endforeach
                <a href="{{ route('search') }}" class="block px-3 py-2 rounded text-gray-700 hover:bg-blue-50">البحث</a>
                <a href="{{ route('about') }}" class="block px-3 py-2 rounded text-gray-700 hover:bg-blue-50">من نحن</a>
                <a href="{{ route('contact') }}" class="block px-3 py-2 rounded text-gray-700 hover:bg-blue-50">اتصل بنا</a>
            </div>
        </div>
    </header>

    {{-- Header Ad --}}
    <div class="bg-white py-2 text-center">
        @adslot('header_ad')
    </div>

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    {{-- Footer Ad --}}
    <div class="bg-white py-2 text-center border-t">
        @adslot('footer_ad')
    </div>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-white mt-8">
        <div class="max-w-7xl mx-auto px-4 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Site Info --}}
                <div>
                    <h3 class="text-xl font-bold mb-3">🎮 {{ $globalSettings['site_name'] }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $globalSettings['site_description'] }}</p>
                    {{-- Social Links --}}
                    <div class="flex gap-3 mt-4">
                        @if($globalSettings['facebook_url'])
                        <a href="{{ $globalSettings['facebook_url'] }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-blue-400" aria-label="فيسبوك">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        @endif
                        @if($globalSettings['twitter_url'])
                        <a href="{{ $globalSettings['twitter_url'] }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-sky-400" aria-label="تويتر">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        @endif
                        @if($globalSettings['telegram_url'])
                        <a href="{{ $globalSettings['telegram_url'] }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-blue-400" aria-label="تيليغرام">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                        </a>
                        @endif
                        @if($globalSettings['whatsapp_number'])
                        <a href="https://wa.me/{{ $globalSettings['whatsapp_number'] }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-green-400" aria-label="واتساب">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="font-bold mb-3 text-gray-200">روابط سريعة</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">الرئيسية</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition">من نحن</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">اتصل بنا</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition">سياسة الخصوصية</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-white transition">شروط الاستخدام</a></li>
                    </ul>
                </div>

                {{-- Categories --}}
                <div>
                    <h4 class="font-bold mb-3 text-gray-200">التصنيفات</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        @foreach(\App\Models\Category::whereNull('parent_id')->orderBy('sort_order')->limit(6)->get() as $cat)
                        <li><a href="{{ route('category.show', $cat->slug) }}" class="hover:text-white transition">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-500 text-sm">
                {!! $globalSettings['footer_text'] !!}
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
