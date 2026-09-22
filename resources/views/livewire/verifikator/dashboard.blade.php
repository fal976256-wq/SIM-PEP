<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800"><i class="fas fa-check-double text-blue-600"></i> Dashboard Verifikator</h2>
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

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600"></i>
                </div>
                <span class="text-xs text-gray-400">Semua</span>
            </div>
            <div class="text-3xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
            <div class="text-sm font-medium text-gray-500 mt-1">Total Usulan</div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-blue-400">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-search text-blue-500"></i>
                </div>
                @if(($stats['review'] ?? 0) > 0)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                    {{ $stats['review'] }}
                </span>
                @endif
            </div>
            <div class="text-3xl font-bold text-blue-600">{{ $stats['review'] ?? 0 }}</div>
            <div class="text-sm font-medium text-gray-500 mt-1">Review Dinas</div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-orange-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-orange-600"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-orange-600">{{ $stats['cleared'] ?? 0 }}</div>
            <div class="text-sm font-medium text-gray-500 mt-1">Cleared RKA</div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['approved'] ?? 0 }}</div>
            <div class="text-sm font-medium text-gray-500 mt-1">Final Disetujui</div>
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
                    <div class="text-sm text-gray-500">Usulan KUBE</div>
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
                    <div class="text-sm text-gray-500">Usulan UEP</div>
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

    {{-- Kanban CTA --}}
    <a href="{{ route('verifikator.verifikasi') }}" wire:navigate
       class="block bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 rounded-xl shadow-sm p-6 mb-6 text-white hover:shadow-lg transition-all duration-200 group">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/15 rounded-xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/25 transition-colors">
                    <i class="fas fa-columns text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xl">Kanban Board Verifikasi</h3>
                    <p class="text-blue-100 text-sm mt-0.5">
                        @if(($stats['review'] ?? 0) > 0)
                            <span class="inline-flex items-center px-2 py-0.5 bg-white/20 rounded-full text-xs font-bold mr-1">{{ $stats['review'] }}</span>
                        @endif
                        usulan menunggu verifikasi Anda
                    </p>
                </div>
            </div>
            <div class="w-10 h-10 bg-white/15 rounded-lg flex items-center justify-center group-hover:bg-white/25 group-hover:translate-x-1 transition-all">
                <i class="fas fa-arrow-right text-lg"></i>
            </div>
        </div>
    </a>

    {{-- Recent Usulan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-bold text-gray-800"><i class="fas fa-clock text-gray-400 mr-2"></i>Usulan Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Usulan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Desil</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Utusan</th>
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
                            @if($usulan->desil)
                            <span class="inline-flex items-center gap-1">
                                @if($usulan->is_desil_valid ?? false)
                                <i class="fas fa-check-circle text-green-500 text-xs"></i>
                                @endif
                                <span class="font-bold {{ $usulan->desil <= 4 ? 'text-gray-800' : 'text-red-600' }}">{{ $usulan->desil }}</span>
                            </span>
                            @else
                            <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $usulan->status->bgColor() }}">
                                {{ $usulan->status->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-gray-500">{{ $usulan->user?->name ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-500 text-sm">{{ $usulan->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-inbox text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-gray-400 font-medium">Belum ada usulan</p>
                                <p class="text-sm text-gray-300 mt-1">Usulan akan muncul di sini setelah ada pengajuan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
