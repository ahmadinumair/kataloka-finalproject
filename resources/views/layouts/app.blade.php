<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F7F8F5]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'kataloka — Platform Aspirasi & Feedback Kampus' }}</title>

    <!-- Google Fonts: Syne (Brand/Headers) & Space Grotesk / Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-syne { font-family: 'Syne', sans-serif; }
        .font-space { font-family: 'Space Grotesk', sans-serif; }
        
        /* Livewire v4 Instant SPA Top Progress Bar */
        #nprogress .bar,
        .livewire-progress-bar {
            background: #B5F938 !important;
            height: 3px !important;
            box-shadow: 0 0 10px #B5F938, 0 0 5px #B5F938 !important;
        }
    </style>
</head>
<body class="min-h-full flex flex-col text-slate-900 bg-[#F7F8F5] antialiased selection:bg-[#B5F938] selection:text-black">

    <!-- Global Top SPA Navigation Indicator (Shows during wire:navigate) -->
    <div id="spa-nav-loader" class="fixed top-0 left-0 right-0 h-1 bg-[#B5F938] z-50 transition-all duration-300 opacity-0 pointer-events-none shadow-[0_0_10px_#B5F938]"></div>

    <!-- Top Navigation Bar (Inspired by Minimalist Clean Header) -->
    <header class="sticky top-0 z-40 bg-[#F7F8F5]/90 backdrop-blur-md border-b border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">

                <!-- Brand / Logo (Ikon Unik & Font Beda) -->
                <div class="flex items-center gap-8">
                    <a href="{{ route('feedbacks.index') }}" wire:navigate class="flex items-center gap-2.5 group">
                        <!-- Custom Icon: Black circle with 3 vibrant lime sound/speech slashes -->
                        <div class="w-10 h-10 rounded-full bg-black flex items-center justify-center text-[#B5F938] shadow-sm group-hover:scale-105 transition-transform duration-300">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <line x1="7" y1="15" x2="7" y2="9"></line>
                                <line x1="12" y1="18" x2="12" y2="6"></line>
                                <line x1="17" y1="14" x2="17" y2="10"></line>
                            </svg>
                        </div>
                        <div class="flex items-baseline">
                            <span class="font-syne text-2xl font-extrabold tracking-tight text-black group-hover:opacity-90 transition-opacity">
                                kata<span class="text-slate-900">loka</span><span class="inline-block w-2 h-2 rounded-full bg-[#B5F938] ml-1"></span>
                            </span>
                        </div>
                    </a>

                    <!-- Navigation Links (Instant SPA with wire:navigate) -->
                    <nav class="hidden md:flex items-center gap-1">
                        <a href="{{ route('feedbacks.index') }}" wire:navigate
                            class="px-4 py-2 rounded-full text-xs font-space font-semibold transition-all {{ request()->routeIs('feedbacks.index') ? 'bg-black text-white shadow-sm' : 'text-slate-700 hover:text-black hover:bg-black/5' }}">
                            Semua Aspirasi
                        </a>
                        <a href="{{ auth()->check() ? route('feedbacks.create') : route('login') }}" wire:navigate
                            class="px-4 py-2 rounded-full text-xs font-space font-semibold transition-all {{ request()->routeIs('feedbacks.create') ? 'bg-[#B5F938] text-black shadow-sm font-bold' : 'text-slate-700 hover:text-black hover:bg-black/5' }}">
                            + Buat Aspirasi
                        </a>
                        @php
                            $navSurvey = \App\Models\Survey::where('is_active', true)->latest()->first();
                        @endphp
                        @if($navSurvey)
                            <a href="{{ route('surveys.fill', $navSurvey->id) }}" wire:navigate
                                class="px-4 py-2 rounded-full text-xs font-space font-semibold transition-all {{ request()->routeIs('surveys.fill') ? 'bg-black text-[#B5F938] shadow-sm font-bold' : 'text-slate-700 hover:text-black hover:bg-black/5' }}">
                                📋 Kuesioner Mahasiswa
                            </a>
                        @endif

                        @auth
                            @if(auth()->user()->role === 'admin')
                                <span class="h-4 w-px bg-slate-300 mx-1"></span>
                                <a href="{{ route('admin.dashboard') }}" wire:navigate
                                    class="px-4 py-2 rounded-full text-xs font-space font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-black text-white' : 'text-slate-700 hover:text-black hover:bg-black/5' }}">
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.feedbacks') }}" wire:navigate
                                    class="px-4 py-2 rounded-full text-xs font-space font-semibold transition-all {{ request()->routeIs('admin.feedbacks') ? 'bg-black text-white' : 'text-slate-700 hover:text-black hover:bg-black/5' }}">
                                    Kelola Data
                                </a>
                            @endif
                        @endauth
                    </nav>
                </div>

                <!-- Right Side: Auth -->
                <div class="flex items-center gap-3">
                    @auth
                        <div class="flex items-center gap-3">
                            <div class="text-right hidden sm:block">
                                <p class="text-xs font-bold text-black font-space leading-tight">{{ auth()->user()->name }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold font-space uppercase {{ auth()->user()->role === 'admin' ? 'bg-[#B5F938] text-black' : 'bg-slate-200 text-slate-800' }}">
                                    {{ auth()->user()->role }}
                                </span>
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-xs font-space font-bold text-slate-700 hover:text-rose-600 bg-white border border-slate-200 rounded-full hover:border-rose-200 transition-colors shadow-2xs">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}" wire:navigate class="px-5 py-2 text-xs font-space font-bold text-slate-800 hover:text-black rounded-full hover:bg-black/5 transition-colors">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" wire:navigate class="px-5 py-2 text-xs font-space font-bold text-black bg-[#B5F938] hover:bg-[#a5f123] rounded-full shadow-sm hover:shadow transition-all">
                                Daftar Sekarang
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile sub-navigation -->
        <div class="md:hidden flex items-center justify-around border-t border-slate-200/60 py-2.5 px-4 bg-white/70 text-xs font-space">
            <a href="{{ route('feedbacks.index') }}" wire:navigate class="font-bold {{ request()->routeIs('feedbacks.index') ? 'text-black bg-[#B5F938] px-3 py-1 rounded-full' : 'text-slate-600' }}">
                Aspirasi
            </a>
            <a href="{{ auth()->check() ? route('feedbacks.create') : route('login') }}" wire:navigate class="font-bold {{ request()->routeIs('feedbacks.create') ? 'text-black bg-[#B5F938] px-3 py-1 rounded-full' : 'text-slate-600' }}">
                + Tulis
            </a>
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="font-bold {{ request()->routeIs('admin.dashboard') ? 'text-white bg-black px-3 py-1 rounded-full' : 'text-slate-600' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.feedbacks') }}" wire:navigate class="font-bold {{ request()->routeIs('admin.feedbacks') ? 'text-white bg-black px-3 py-1 rounded-full' : 'text-slate-600' }}">
                        Kelola
                    </a>
                @endif
            @endauth
        </div>
    </header>

    <!-- Global Floating Toast / Alert Notifications (Auto-dismiss 4 Detik dengan Alpine.js) -->
    <div class="fixed top-24 right-4 sm:right-8 z-50 flex flex-col gap-2.5 max-w-sm sm:max-w-md w-full pointer-events-none">
        @if (session()->has('success'))
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => show = false, 4000)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 class="pointer-events-auto flex items-center justify-between p-4 text-xs font-space font-bold text-black rounded-2xl bg-[#B5F938] border border-black/10 shadow-xl shadow-black/10"
                 role="alert">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-black text-[#B5F938] flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                    <span class="leading-relaxed">{{ session('success') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-black/60 hover:text-black font-bold p-1 ml-2 transition-colors shrink-0">
                    ✕
                </button>
            </div>
        @endif

        @if (session()->has('ai_notice'))
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => show = false, 4000)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 class="pointer-events-auto flex items-center justify-between p-4 text-xs font-space font-bold text-white rounded-2xl bg-black border border-white/10 shadow-xl shadow-black/15"
                 role="alert">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-[#B5F938] text-black flex items-center justify-center font-bold text-xs shrink-0">⚡</span>
                    <span class="leading-relaxed">{{ session('ai_notice') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-white/60 hover:text-white font-bold p-1 ml-2 transition-colors shrink-0">
                    ✕
                </button>
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => show = false, 4500)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 class="pointer-events-auto flex items-center justify-between p-4 text-xs font-space font-bold text-white rounded-2xl bg-rose-600 border border-rose-700 shadow-xl shadow-rose-950/20"
                 role="alert">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-white text-rose-600 flex items-center justify-center font-bold text-xs shrink-0">✕</span>
                    <span class="leading-relaxed">{{ session('error') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-white/60 hover:text-white font-bold p-1 ml-2 transition-colors shrink-0">
                    ✕
                </button>
            </div>
        @endif
    </div>

    <!-- Main Content Body -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 font-space">
            <div class="flex items-center gap-2">
                <span class="font-syne text-lg font-bold text-black">kata<span class="text-slate-900">loka</span><span class="inline-block w-1.5 h-1.5 rounded-full bg-[#B5F938] ml-0.5"></span></span>
                <span>— Platform Aspirasi Kampus Cerdas</span>
            </div>
            <p>&copy; {{ date('Y') }} Kataloka. Terintegrasi Analisis AI & Livewire.</p>
        </div>
    </footer>

    @livewireScripts
    <script>
        document.addEventListener('livewire:navigating', () => {
            const bar = document.getElementById('spa-nav-loader');
            if (bar) {
                bar.classList.remove('opacity-0');
                bar.classList.add('opacity-100', 'animate-pulse');
            }
        });
        document.addEventListener('livewire:navigated', () => {
            const bar = document.getElementById('spa-nav-loader');
            if (bar) {
                bar.classList.remove('opacity-100', 'animate-pulse');
                bar.classList.add('opacity-0');
            }
        });
    </script>
</body>
</html>
