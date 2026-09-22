<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIM-PEP') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100" x-data="{ sidebarOpen: true, mobileOpen: false, isDesktop: window.innerWidth >= 1024 }" @resize.window="isDesktop = window.innerWidth >= 1024; if(isDesktop) mobileOpen = false">
    {{-- Skip to content --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:bg-blue-600 focus:text-white focus:px-4 focus:py-2 focus:rounded-lg">
        Skip to content
    </a>

    <div class="min-h-screen flex">
        {{-- Mobile Overlay --}}
        <div x-show="mobileOpen" @click="mobileOpen = false"
             class="fixed inset-0 bg-black/50 z-40 lg:hidden"
             x-transition:enter="transition-opacity duration-300"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-300"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             x-cloak></div>

        {{-- Sidebar --}}
        @auth
        <aside aria-label="Main navigation"
               class="bg-gray-900 text-white flex-shrink-0 flex flex-col
                      transition-[width,transform] duration-300 ease-in-out
                      overflow-y-auto overflow-x-hidden"
               :class="{
                   // Mobile: fixed overlay
                   'fixed inset-y-0 left-0 z-50': !isDesktop,
                   'w-64 translate-x-0': !isDesktop && mobileOpen,
                   'w-0 -translate-x-full pointer-events-none': !isDesktop && !mobileOpen,
                   // Desktop: inline flex child, never fixed
                   'lg:w-64 lg:translate-x-0 lg:pointer-events-auto': isDesktop && sidebarOpen,
                   'lg:w-16 lg:translate-x-0 lg:pointer-events-auto': isDesktop && !sidebarOpen
               }">
            {{-- Sidebar Header --}}
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-700 flex-shrink-0">
                <div class="flex items-center gap-3 overflow-hidden whitespace-nowrap" x-show="sidebarOpen" x-cloak>
                    <img src="/images/logo-sulbar.svg" alt="Logo Pemprov Sulbar" class="w-9 h-9 flex-shrink-0 object-contain">
                    <div class="flex flex-col min-w-0">
                        <span class="text-xs font-bold text-white leading-tight truncate">SIM-PEP</span>
                        <span class="text-[10px] text-gray-400 leading-tight truncate">Prov. Sulawesi Barat</span>
                    </div>
                </div>
                <div class="flex-shrink-0" :class="sidebarOpen ? 'ml-auto' : 'mx-auto'">
                    <button @click="sidebarOpen = !sidebarOpen; mobileOpen = false" aria-label="Toggle sidebar"
                            class="text-gray-400 hover:text-white hidden lg:block">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  :d="sidebarOpen ? 'M11 19l-7-7 7-7m8 14l-7-7 7-7' : 'M13 5l7 7-7 7M5 5l7 7-7 7'"/>
                        </svg>
                    </button>
                    <button @click="mobileOpen = false" aria-label="Close sidebar"
                            class="text-gray-400 hover:text-white lg:hidden">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            @php $user = auth()->user(); @endphp

            {{-- User Profile --}}
            <div x-show="sidebarOpen" x-cloak class="px-4 py-4 border-b border-gray-700 flex-shrink-0">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-white truncate">{{ $user->name }}</div>
                        <div class="text-xs text-gray-400 truncate">
                            @if($user->anggotaDewan)
                                {{ $user->anggotaDewan->partai }} — {{ $user->anggotaDewan->dapil }}
                            @else
                                {{ $user->role?->label() ?? 'User' }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Menu Utusan Dewan --}}
            @if($user->isUtusanDewan())
            <nav aria-label="Menu Utusan Dewan" class="mt-4 px-2 space-y-1">
                <p x-show="sidebarOpen" x-cloak class="px-3 py-2 text-xs text-gray-500 uppercase tracking-wider whitespace-nowrap">Menu Utama</p>
                <a href="{{ route('utusan.dashboard') }}" wire:navigate
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition whitespace-nowrap {{ request()->routeIs('utusan.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-chart-pie w-5 text-center flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-cloak>Dashboard</span>
                </a>
                <a href="{{ route('utusan.pengajuan.kube') }}" wire:navigate
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition whitespace-nowrap {{ request()->routeIs('utusan.pengajuan.kube') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-box w-5 text-center flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-cloak>Pengajuan KUBE</span>
                </a>
                <a href="{{ route('utusan.pengajuan.uep') }}" wire:navigate
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition whitespace-nowrap {{ request()->routeIs('utusan.pengajuan.uep') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-money-bill w-5 text-center flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-cloak>Pengajuan UEP</span>
                </a>
                <a href="{{ route('utusan.usulan-saya') }}" wire:navigate
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition whitespace-nowrap {{ request()->routeIs('utusan.usulan-saya') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-list w-5 text-center flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-cloak>Daftar Usulan Saya</span>
                </a>
            </nav>
            @endif

            {{-- Menu Verifikator --}}
            @if($user->canVerify())
            <nav aria-label="Menu Verifikator" class="mt-4 px-2 space-y-1">
                <p x-show="sidebarOpen" x-cloak class="px-3 py-2 text-xs text-gray-500 uppercase tracking-wider whitespace-nowrap">Menu Utama</p>
                <a href="{{ route('verifikator.dashboard') }}" wire:navigate
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition whitespace-nowrap {{ request()->routeIs('verifikator.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-chart-pie w-5 text-center flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-cloak>Dashboard</span>
                </a>
                <a href="{{ route('verifikator.verifikasi') }}" wire:navigate
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition whitespace-nowrap {{ request()->routeIs('verifikator.verifikasi') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-check-double w-5 text-center flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-cloak>Verifikasi Usulan</span>
                </a>

                <p x-show="sidebarOpen" x-cloak class="px-3 py-2 text-xs text-gray-500 uppercase tracking-wider mt-4 whitespace-nowrap">Master Data</p>
                <a href="{{ route('verifikator.master-pagu') }}" wire:navigate
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition whitespace-nowrap {{ request()->routeIs('verifikator.master-pagu') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-database w-5 text-center flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-cloak>Master Pagu KUBE</span>
                </a>
                <a href="{{ route('verifikator.master-desil') }}" wire:navigate
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition whitespace-nowrap {{ request()->routeIs('verifikator.master-desil') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-users w-5 text-center flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-cloak>Data Desil DTSEN</span>
                </a>

                <p x-show="sidebarOpen" x-cloak class="px-3 py-2 text-xs text-gray-500 uppercase tracking-wider mt-4 whitespace-nowrap">Pelaporan</p>
                <a href="{{ route('verifikator.laporan') }}" wire:navigate
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition whitespace-nowrap {{ request()->routeIs('verifikator.laporan') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-file-csv w-5 text-center flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-cloak>Laporan & Arsip</span>
                </a>
            </nav>
            @endif

            {{-- Menu Admin Prov --}}
            @if($user->isAdminProv())
            <nav aria-label="Menu Admin" class="mt-4 px-2 space-y-1">
                <p x-show="sidebarOpen" x-cloak class="px-3 py-2 text-xs text-gray-500 uppercase tracking-wider whitespace-nowrap">Manajemen</p>
                <a href="{{ route('admin.dashboard') }}" wire:navigate
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-chart-pie w-5 text-center flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-cloak>Dashboard Admin</span>
                </a>
                <a href="{{ route('admin.manajemen-user') }}" wire:navigate
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition whitespace-nowrap {{ request()->routeIs('admin.manajemen-user') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-users-cog w-5 text-center flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-cloak>Manajemen User</span>
                </a>
                <a href="{{ route('admin.gdrive') }}" wire:navigate
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition whitespace-nowrap {{ request()->routeIs('admin.gdrive') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fab fa-google-drive w-5 text-center flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-cloak>Google Drive</span>
                </a>
            </nav>
            @endif

            {{-- Spacer to push footer down --}}
            <div class="flex-1"></div>

            {{-- Sidebar Footer --}}
            <div class="px-2 py-4 border-t border-gray-700 flex-shrink-0 space-y-1">
                <a href="{{ route('profile.show') }}" wire:navigate
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-gray-800 transition whitespace-nowrap">
                    <i class="fas fa-cog w-5 text-center flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-cloak>Pengaturan</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                       class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-red-600/20 hover:text-red-400 transition whitespace-nowrap">
                        <i class="fas fa-sign-out-alt w-5 text-center flex-shrink-0"></i>
                        <span x-show="sidebarOpen" x-cloak>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>
        @endauth

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-h-screen min-w-0">
            {{-- Mobile Header --}}
            @auth
            <header class="bg-white shadow-sm border-b lg:hidden flex-shrink-0">
                <div class="flex items-center justify-between px-4 py-3">
                    <button @click="mobileOpen = !mobileOpen" aria-label="Open menu" :aria-expanded="mobileOpen"
                            class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex items-center gap-2">
                        <img src="/images/logo-sulbar.svg" alt="Logo Pemprov Sulbar" class="w-8 h-8 object-contain">
                        <span class="font-bold text-blue-700 text-sm">SIM-PEP</span>
                    </div>
                    <div class="flex items-center gap-3">
                        @php $unreadCount = \App\Models\Notifikasi::unreadCount(auth()->id()); @endphp
                        <div class="relative" x-data="{ openNotif: false }">
                            <button @click="openNotif = !openNotif" :aria-expanded="openNotif" aria-label="Notifikasi" class="text-gray-500 hover:text-gray-700 relative">
                                <i class="fas fa-bell text-xl"></i>
                                @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                @endif
                            </button>
                             <div x-show="openNotif" @click.away="openNotif = false" @keydown.escape.window="openNotif = false" x-cloak
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border z-50 max-h-80 overflow-y-auto">
                                <div class="p-3 border-b">
                                    <span class="text-sm font-bold text-gray-800">Notifikasi</span>
                                </div>
                                @php $notifs = \App\Models\Notifikasi::forUser(auth()->id())->latest()->take(20)->get(); @endphp
                                @forelse($notifs as $notif)
                                <div class="px-3 py-2 border-b last:border-0 {{ $notif->is_read ? 'bg-white' : 'bg-blue-50' }}">
                                    <div class="text-xs font-medium {{ $notif->tipe === 'warning' ? 'text-yellow-700' : ($notif->tipe === 'success' ? 'text-green-700' : 'text-blue-700') }}">{{ $notif->judul }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $notif->pesan }}</div>
                                    <div class="text-[10px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                                </div>
                                @empty
                                <div class="p-4 text-center text-xs text-gray-400">Tidak ada notifikasi</div>
                                @endforelse
                            </div>
                        </div>
                        <a href="{{ route('profile.show') }}" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-cog text-xl"></i>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" aria-label="Keluar" class="text-gray-500 hover:text-red-600">
                                <i class="fas fa-sign-out-alt text-xl"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>
            @endauth

            @if (isset($header))
                <header class="bg-white shadow-sm border-b hidden lg:block flex-shrink-0">
                    <div class="px-6 py-4">
                        {!! $header !!}
                    </div>
                </header>
            @endif

            <main id="main-content" class="flex-1 p-4 lg:p-6 overflow-x-auto">
                {{-- Flash Messages --}}
                @if (session()->has('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            {{ session('success') }}
                            <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ session('error') }}
                            <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
