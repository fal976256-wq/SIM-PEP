<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800"><i class="fas fa-shield-alt text-purple-600"></i> Dashboard Admin Provinsi</h2>
            <span class="text-sm text-gray-500 hidden sm:inline">Tahun {{ date('Y') }}</span>
        </div>
    </x-slot>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-purple-600"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
            <div class="text-sm font-medium text-gray-500 mt-1">Total Usulan</div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-search text-blue-600"></i>
                </div>
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

    {{-- User Distribution + Status Distribution --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        {{-- User Cards --}}
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="text-sm font-semibold text-gray-700 mb-4"><i class="fas fa-users text-gray-400 mr-2"></i>Distribusi Pengguna</div>
            <div class="grid grid-cols-3 gap-3">
                <div class="text-center p-3 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-users text-gray-500"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_users'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total</div>
                </div>
                <div class="text-center p-3 bg-blue-50 rounded-xl">
                    <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-user-tie text-blue-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['utusan_dewan'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500 mt-1">Utusan</div>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-xl">
                    <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-user-check text-green-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-green-600">{{ $stats['verifikator'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500 mt-1">Verifikator</div>
                </div>
            </div>
        </div>

        {{-- Status Distribution --}}
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="text-sm font-semibold text-gray-700 mb-4"><i class="fas fa-chart-pie text-gray-400 mr-2"></i>Distribusi Status</div>
            @php
                $total = max($stats['total'] ?? 1, 1);
                $draft = $statusCounts['DRAFT'] ?? 0;
                $review = $statusCounts['REVIEW_DINAS'] ?? 0;
                $revisi = $statusCounts['REVISI_UTUSAN'] ?? 0;
                $cleared = $statusCounts['CLEARED_RKA'] ?? 0;
                $approved = $statusCounts['FINAL_APPROVED'] ?? 0;
            @endphp
            <div class="space-y-3">
                {{-- Draft --}}
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="flex items-center gap-1.5 text-gray-600"><span class="w-2 h-2 rounded-full bg-gray-400"></span> Draft</span>
                        <span class="font-semibold text-gray-700">{{ $draft }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gray-400 rounded-full transition-all duration-500" style="width: {{ ($draft / $total) * 100 }}%"></div>
                    </div>
                </div>
                {{-- Review --}}
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="flex items-center gap-1.5 text-gray-600"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Review Dinas</span>
                        <span class="font-semibold text-blue-600">{{ $review }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full transition-all duration-500" style="width: {{ ($review / $total) * 100 }}%"></div>
                    </div>
                </div>
                {{-- Revisi --}}
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="flex items-center gap-1.5 text-gray-600"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> Revisi Utusan</span>
                        <span class="font-semibold text-yellow-600">{{ $revisi }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-yellow-500 rounded-full transition-all duration-500" style="width: {{ ($revisi / $total) * 100 }}%"></div>
                    </div>
                </div>
                {{-- Cleared --}}
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="flex items-center gap-1.5 text-gray-600"><span class="w-2 h-2 rounded-full bg-orange-500"></span> Cleared RKA</span>
                        <span class="font-semibold text-orange-600">{{ $cleared }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-orange-500 rounded-full transition-all duration-500" style="width: {{ ($cleared / $total) * 100 }}%"></div>
                    </div>
                </div>
                {{-- Approved --}}
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="flex items-center gap-1.5 text-gray-600"><span class="w-2 h-2 rounded-full bg-green-500"></span> Final Disetujui</span>
                        <span class="font-semibold text-green-600">{{ $approved }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 rounded-full transition-all duration-500" style="width: {{ ($approved / $total) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <a href="{{ route('verifikator.verifikasi') }}" wire:navigate
           class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-green-400 transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-green-200 transition-colors">
                    <i class="fas fa-check-double text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 group-hover:text-green-600 transition-colors">Verifikasi Usulan</h3>
                    <p class="text-sm text-gray-500">Kanban Board terpadu</p>
                </div>
                <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-green-500 group-hover:translate-x-1 transition-all"></i>
            </div>
        </a>

        <a href="{{ route('verifikator.master-pagu') }}" wire:navigate
           class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-yellow-400 transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-yellow-200 transition-colors">
                    <i class="fas fa-coins text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 group-hover:text-yellow-600 transition-colors">Master Pagu</h3>
                    <p class="text-sm text-gray-500">Kelola pagu KUBE</p>
                </div>
                <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-yellow-500 group-hover:translate-x-1 transition-all"></i>
            </div>
        </a>

        <a href="{{ route('admin.manajemen-user') }}" wire:navigate
           class="stat-card group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-purple-400 transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-purple-200 transition-colors">
                    <i class="fas fa-users-cog text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 group-hover:text-purple-600 transition-colors">Manajemen User</h3>
                    <p class="text-sm text-gray-500">Kelola akun pengguna</p>
                </div>
                <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-purple-500 group-hover:translate-x-1 transition-all"></i>
            </div>
        </a>
    </div>

    {{-- Recent Usulan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-bold text-gray-800"><i class="fas fa-clock text-gray-400 mr-2"></i>Usulan Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Usulan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Desil</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Utusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentUsulan as $usulan)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4 text-gray-400 text-sm font-mono">#{{ $usulan->id }}</td>
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-inbox text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-gray-400 font-medium">Belum ada usulan</p>
                                <p class="text-sm text-gray-300 mt-1">Data usulan akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
