<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800"><i class="fas fa-chart-line text-blue-600"></i> Dashboard</h2>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 hidden sm:inline">Tahun Pengajuan</span>
                <select wire:model.live="tahunPengajuan" class="border-gray-300 rounded-lg shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach($tahunOptions as $thn)
                    <option value="{{ $thn }}">{{ $thn }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-slot>

    {{-- Welcome Banner --}}
    @php
        $userName = auth()->user()->name;
    @endphp
    <div class="bg-gradient-to-r from-blue-50 via-white to-transparent rounded-xl border border-blue-100 p-5 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Selamat datang, {{ $userName }} <span class="inline-block">👋</span></h3>
                <p class="text-sm text-gray-500 mt-1">Kelola usulan pengajuan bantuan Anda di sini.</p>
            </div>
            <div class="hidden md:block text-right">
                <div class="text-3xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</div>
                <div class="text-xs text-gray-500">Total Usulan Tahun Ini</div>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
            <div class="text-sm font-medium text-gray-500 mt-1">Total Usulan</div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-gray-400">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pencil-alt text-gray-500"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-600">{{ $stats['draft'] ?? 0 }}</div>
            <div class="text-sm font-medium text-gray-500 mt-1">Draft</div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-undo text-yellow-600"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-yellow-600">{{ $stats['revisi'] ?? 0 }}</div>
            <div class="text-sm font-medium text-gray-500 mt-1">Perlu Revisi</div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['approved'] ?? 0 }}</div>
            <div class="text-sm font-medium text-gray-500 mt-1">Disetujui</div>
        </div>
    </div>

    {{-- KUBE vs UEP + Status Distribution --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        {{-- KUBE Count --}}
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['kube'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Pengajuan KUBE</div>
                </div>
            </div>
        </div>

        {{-- UEP Count --}}
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-money-bill-wave text-emerald-600 text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['uep'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Pengajuan UEP</div>
                </div>
            </div>
        </div>

        {{-- Status Distribution --}}
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="text-sm font-semibold text-gray-700 mb-3">Distribusi Status</div>
            @php
                $total = max($stats['total'] ?? 1, 1);
                $draft = $statusCounts['DRAFT'] ?? 0;
                $review = $statusCounts['REVIEW_DINAS'] ?? 0;
                $revisi = $statusCounts['REVISI_UTUSAN'] ?? 0;
                $cleared = $statusCounts['CLEARED_RKA'] ?? 0;
                $approved = $statusCounts['FINAL_APPROVED'] ?? 0;
            @endphp
            <div class="status-bar mb-3">
                @if($draft > 0)
                <div class="status-bar-segment bg-gray-400" style="width: {{ ($draft / $total) * 100 }}%" title="Draft: {{ $draft }}"></div>
                @endif
                @if($review > 0)
                <div class="status-bar-segment bg-blue-500" style="width: {{ ($review / $total) * 100 }}%" title="Review: {{ $review }}"></div>
                @endif
                @if($revisi > 0)
                <div class="status-bar-segment bg-yellow-500" style="width: {{ ($revisi / $total) * 100 }}%" title="Revisi: {{ $revisi }}"></div>
                @endif
                @if($cleared > 0)
                <div class="status-bar-segment bg-orange-500" style="width: {{ ($cleared / $total) * 100 }}%" title="Cleared: {{ $cleared }}"></div>
                @endif
                @if($approved > 0)
                <div class="status-bar-segment bg-green-500" style="width: {{ ($approved / $total) * 100 }}%" title="Approved: {{ $approved }}"></div>
                @endif
            </div>
            <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-gray-400"></span> Draft {{ $draft }}</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Review {{ $review }}</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> Revisi {{ $revisi }}</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-orange-500"></span> Cleared {{ $cleared }}</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> Approved {{ $approved }}</span>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <a href="{{ route('utusan.pengajuan.kube') }}" wire:navigate
           class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-blue-400 transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fas fa-plus text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 group-hover:text-blue-600 transition-colors">Pengajuan KUBE</h3>
                    <p class="text-sm text-gray-500">Ajukan bantuan untuk kelompok usaha</p>
                </div>
                <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-blue-500 group-hover:translate-x-1 transition-all"></i>
            </div>
        </a>

        <a href="{{ route('utusan.pengajuan.uep') }}" wire:navigate
           class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-emerald-400 transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fas fa-plus text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 group-hover:text-emerald-600 transition-colors">Pengajuan UEP</h3>
                    <p class="text-sm text-gray-500">Ajukan bantuan tunai untuk individu</p>
                </div>
                <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-emerald-500 group-hover:translate-x-1 transition-all"></i>
            </div>
        </a>
    </div>

    {{-- Recent Usulan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800"><i class="fas fa-clock text-gray-400 mr-2"></i>Usulan Terbaru</h3>
            <a href="{{ route('utusan.usulan-saya') }}" wire:navigate class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                Lihat Semua <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Usulan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentUsulan as $usulan)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-800">{{ $usulan->nama_kelompok_usaha ?? $usulan->nama_ketua_individu }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $usulan->bidang_usaha ?? '-' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $usulan->isKube() ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $usulan->jenis_bantuan->value }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $usulan->status->bgColor() }}">
                                {{ $usulan->status->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-gray-500 text-sm">{{ $usulan->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-inbox text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-gray-400 font-medium">Belum ada usulan</p>
                                <p class="text-sm text-gray-300 mt-1">Mulai buat pengajuan baru untuk memulai</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
