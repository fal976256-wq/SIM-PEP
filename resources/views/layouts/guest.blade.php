<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIM-PEP') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 via-white to-green-50">
            {{-- SIM-PEP Branding --}}
            <div class="text-center mb-6">
                <img src="/images/logo-sulbar.svg" alt="Logo Pemprov Sulbar" class="w-20 h-20 mx-auto mb-3 object-contain">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pemerintah Daerah Provinsi Sulawesi Barat</p>
                <p class="text-[11px] font-semibold text-gray-600 mt-1.5">Dinas Sosial Pemberdayaan Perempuan dan Anak serta Pemberdayaan Masyarakat Desa</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Bidang Pemberdayaan Sosial dan Penanganan Kemiskinan</p>
                <h1 class="text-lg font-bold text-gray-800 mt-2">SIM-PEP</h1>
                <p class="text-xs text-gray-400 mt-0.5">Sistem Informasi Manajemen Pokir Pemberdayaan Ekonomi Produktif</p>
            </div>

            <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-xl sm:rounded-2xl border border-gray-100">
                {{ $slot }}
            </div>

            <p class="text-xs text-gray-400 mt-6">Pemerintah Daerah Provinsi Sulawesi Barat &copy; {{ date('Y') }}</p>
        </div>
    </body>
</html>
