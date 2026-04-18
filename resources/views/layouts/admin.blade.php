<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'لوحة التحكم') | لوحة تحكم ألعاب الكمبيوتر</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ ($globalSettings['favicon'] ?? '') ? asset('storage/'.$globalSettings['favicon']) : asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ ($globalSettings['favicon'] ?? '') ? asset('storage/'.$globalSettings['favicon']) : asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @yield('head')
    @stack('head')
    <style>
        /* WordPress-style dark admin */
        :root {
            --wp-sidebar-bg:      #1d2327;
            --wp-sidebar-border:  #2c3338;
            --wp-sidebar-text:    #a7aaad;
            --wp-sidebar-hover:   #72aee6;
            --wp-sidebar-active-bg: #2271b1;
            --wp-sidebar-active-text: #fff;
            --wp-sidebar-section: #646970;
            --wp-topbar-bg:       #1d2327;
            --wp-topbar-text:     #c3c4c7;
            --wp-topbar-hover:    #72aee6;
            --wp-content-bg:      #f0f0f1;
        }
        body { background: var(--wp-content-bg); }

        /* Sidebar */
        #wp-sidebar {
            background: var(--wp-sidebar-bg);
            width: 260px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: width .2s ease;
        }
        #wp-sidebar.collapsed { width: 46px; }
        #wp-sidebar.collapsed .nav-label,
        #wp-sidebar.collapsed .nav-section-label,
        #wp-sidebar.collapsed .sidebar-logo-text,
        #wp-sidebar.collapsed .user-info-text { display: none; }
        #wp-sidebar.collapsed .sidebar-link { justify-content: center; padding: 10px 0; }

        /* Logo */
        .sidebar-logo {
            background: #000;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--wp-sidebar-border);
        }
        .sidebar-logo-icon { color: #fff; font-size: 20px; flex-shrink: 0; }
        .sidebar-logo-text { color: #fff; font-weight: 700; font-size: 14px; line-height: 1.3; }
        .sidebar-logo-text small { display: block; color: var(--wp-sidebar-text); font-weight: 400; font-size: 11px; }

        /* User */
        .sidebar-user {
            padding: 10px 16px;
            border-bottom: 1px solid var(--wp-sidebar-border);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: #2271b1;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 13px; flex-shrink: 0;
        }
        .user-info-text .user-name  { color: #fff; font-size: 13px; font-weight: 600; }
        .user-info-text .user-role  { color: var(--wp-sidebar-text); font-size: 11px; }

        /* Nav sections */
        .nav-section-label {
            color: var(--wp-sidebar-section);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: 16px 16px 4px;
            display: block;
        }

        /* Nav links */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            color: var(--wp-sidebar-text);
            font-size: 13px;
            font-weight: 500;
            transition: color .15s, background .15s;
            border-right: 3px solid transparent;
            text-decoration: none;
        }
        .sidebar-link:hover { color: var(--wp-sidebar-hover); background: rgba(255,255,255,.04); }
        .sidebar-link.active {
            color: var(--wp-sidebar-active-text);
            background: var(--wp-sidebar-active-bg);
            border-right-color: #72aee6;
        }
        .sidebar-link svg { flex-shrink: 0; opacity: .85; }
        .sidebar-link.active svg { opacity: 1; }

        /* English hint */
        .en-hint { font-size: 10px; color: var(--wp-sidebar-section); font-weight: 400; display: block; line-height: 1.1; margin-top: 1px; }
        .sidebar-link.active .en-hint { color: rgba(255,255,255,.6); }
        .sidebar-link:hover .en-hint { color: var(--wp-sidebar-hover); opacity: .7; }

        /* Sidebar footer */
        .sidebar-footer {
            border-top: 1px solid var(--wp-sidebar-border);
            padding: 10px 0;
            margin-top: auto;
        }
        .sidebar-footer a, .sidebar-footer button {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 16px; width: 100%; text-align: right;
            color: var(--wp-sidebar-text); font-size: 12px;
            background: none; border: none; cursor: pointer;
            transition: color .15s;
        }
        .sidebar-footer a:hover, .sidebar-footer button:hover { color: var(--wp-sidebar-hover); }
        .sidebar-footer .logout-btn { color: #f87171; }
        .sidebar-footer .logout-btn:hover { color: #fca5a5; }

        /* Top admin bar */
        #wp-topbar {
            background: var(--wp-topbar-bg);
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .topbar-btn {
            color: var(--wp-topbar-text); background: none; border: none;
            cursor: pointer; padding: 4px; border-radius: 4px;
            display: flex; align-items: center;
            transition: color .15s;
        }
        .topbar-btn:hover { color: var(--wp-topbar-hover); }
        .topbar-title { color: var(--wp-topbar-text); font-size: 14px; font-weight: 600; }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .topbar-greeting { color: var(--wp-topbar-text); font-size: 13px; }
        .topbar-view-site {
            color: var(--wp-topbar-text); font-size: 12px;
            text-decoration: none; display: flex; align-items: center; gap: 4px;
            padding: 4px 8px; border-radius: 4px; border: 1px solid #3c434a;
            transition: color .15s, border-color .15s;
        }
        .topbar-view-site:hover { color: var(--wp-topbar-hover); border-color: var(--wp-topbar-hover); }

        /* Content wrapper */
        #wp-content { display: flex; flex-direction: column; flex: 1; min-height: 100vh; min-width: 0; }
        #wp-main { flex: 1; padding: 20px 24px; overflow: auto; }

        /* Flash */
        .flash-success { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; padding:12px 16px; border-radius:6px; margin-bottom:16px; display:flex; align-items:center; gap:8px; font-size:14px; }
        .flash-error   { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:12px 16px; border-radius:6px; margin-bottom:16px; font-size:14px; }

        /* Page heading strip */
        .wp-page-header {
            background: #fff;
            border-bottom: 1px solid #dcdcde;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .wp-page-header h1 { font-size: 20px; font-weight: 700; color: #1d2327; margin: 0; }
    </style>
</head>
<body x-data="{ sidebarOpen: true }">

<div class="flex min-h-screen">

    {{-- ═══════════════════════════════════
         DARK SIDEBAR (right in RTL)
    ═══════════════════════════════════ --}}
    <aside id="wp-sidebar" :class="sidebarOpen ? '' : 'collapsed'">

        {{-- Logo --}}
        <div class="sidebar-logo">
            <span class="sidebar-logo-icon"><i class="fas fa-gamepad"></i></span>
            <div class="sidebar-logo-text">
                ألعاب الكمبيوتر
                <small>لوحة التحكم — Admin Panel</small>
            </div>
        </div>

        {{-- User --}}
        <div class="sidebar-user">
            <div class="sidebar-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
            <div class="user-info-text">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->role === 'admin' ? 'مدير — Admin' : 'محرر — Editor' }}</div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-2">

            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="nav-label">لوحة التحكم <span class="en-hint">Dashboard</span></span>
            </a>

            {{-- Content --}}
            <span class="nav-section-label">المحتوى — Content</span>

            <a href="{{ route('admin.posts.index') }}" class="sidebar-link {{ request()->routeIs('admin.posts*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="nav-label">المقالات <span class="en-hint">Posts</span></span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span class="nav-label">التصنيفات <span class="en-hint">Categories</span></span>
            </a>
            <a href="{{ route('admin.tags.index') }}" class="sidebar-link {{ request()->routeIs('admin.tags*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span class="nav-label">الوسوم <span class="en-hint">Tags</span></span>
            </a>
            <a href="{{ route('admin.comments.index') }}" class="sidebar-link {{ request()->routeIs('admin.comments*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span class="nav-label">التعليقات <span class="en-hint">Comments</span></span>
            </a>
            <a href="{{ route('admin.media.index') }}" class="sidebar-link {{ request()->routeIs('admin.media*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="nav-label">مكتبة الوسائط <span class="en-hint">Media Library</span></span>
            </a>

            {{-- Downloads --}}
            <span class="nav-section-label">التحميل — Downloads</span>

            <a href="{{ route('admin.download-links.index') }}?post_id=1" class="sidebar-link {{ request()->routeIs('admin.download-links*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span class="nav-label">روابط التحميل <span class="en-hint">Download Links</span></span>
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="sidebar-link {{ request()->routeIs('admin.analytics*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="nav-label">التحليلات <span class="en-hint">Analytics</span></span>
            </a>

            {{-- Settings (admin only) --}}
            @if(auth()->user()->isAdmin())
            <span class="nav-section-label">الإعدادات — Settings</span>

            <a href="{{ route('admin.ad-slots.index') }}" class="sidebar-link {{ request()->routeIs('admin.ad-slots*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span class="nav-label">إدارة الإعلانات <span class="en-hint">Ad Slots</span></span>
            </a>
            <a href="{{ route('admin.seo.index') }}" class="sidebar-link {{ request()->routeIs('admin.seo*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span class="nav-label">إعدادات SEO <span class="en-hint">SEO Settings</span></span>
            </a>
            <a href="{{ route('admin.sitemap.index') }}" class="sidebar-link {{ request()->routeIs('admin.sitemap*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="nav-label">خريطة الموقع <span class="en-hint">Sitemap</span></span>
            </a>
            <a href="{{ route('admin.redirects.index') }}" class="sidebar-link {{ request()->routeIs('admin.redirects*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span class="nav-label">التحويلات <span class="en-hint">Redirects</span></span>
            </a>
            <a href="{{ route('admin.ip-blocks.index') }}" class="sidebar-link {{ request()->routeIs('admin.ip-blocks*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                <span class="nav-label">حظر IP <span class="en-hint">IP Blocks</span></span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span class="nav-label">المستخدمون <span class="en-hint">Users</span></span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="nav-label">الإعدادات <span class="en-hint">Settings</span></span>
            </a>
            @endif

        </nav>

        {{-- Sidebar footer --}}
        <div class="sidebar-footer">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span class="nav-label">تسجيل الخروج <span class="en-hint">Logout</span></span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════════════════════════════
         MAIN CONTENT AREA (left in RTL)
    ═══════════════════════════════════ --}}
    <div id="wp-content">

        {{-- Top admin bar --}}
        <div id="wp-topbar">
            <div class="topbar-left">
                <button class="topbar-btn" @click="sidebarOpen = !sidebarOpen" title="Toggle sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <span class="topbar-title">@yield('title', 'لوحة التحكم')</span>
            </div>
            <div class="topbar-right">
                <span class="topbar-greeting">مرحباً، {{ auth()->user()->name }}</span>
                <a href="{{ route('home') }}" target="_blank" class="topbar-view-site">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    عرض الموقع
                </a>
            </div>
        </div>

        {{-- Page heading --}}
        <div class="wp-page-header">
            <h1>@yield('title', 'لوحة التحكم')</h1>
            @hasSection('header_actions')
            <div>@yield('header_actions')</div>
            @endif
        </div>

        {{-- Flash messages --}}
        <div class="px-6 pt-4">
            @if(session('success'))
            <div class="flash-success">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="flash-error">{{ session('error') }}</div>
            @endif
        </div>

        {{-- Page content --}}
        <main id="wp-main">
            @yield('content')
        </main>
    </div>

</div>

@yield('scripts')
@stack('scripts')
@livewireScripts
</body>
</html>
