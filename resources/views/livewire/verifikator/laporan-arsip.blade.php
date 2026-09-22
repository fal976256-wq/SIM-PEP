<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800"><i class="fas fa-file-alt text-blue-600"></i> Laporan & Arsip</h2>
        </div>
    </x-slot>

    <div class="flex justify-end mb-4">
        <button wire:click="exportCsv" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">
            <span wire:loading.remove wire:target="exportCsv"><i class="fas fa-download me-1"></i> Export CSV</span>
            <span wire:loading wire:target="exportCsv"><i class="fas fa-spinner fa-spin me-1"></i> Exporting...</span>
        </button>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Tahun</label>
                <input type="number" wire:model.live="tahunPengajuan" class="border-gray-300 rounded-lg shadow-sm w-32">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Jenis</label>
                <select wire:model.live="filterJenis" class="border-gray-300 rounded-lg shadow-sm">
                    <option value="all">Semua</option>
                    <option value="KUBE">KUBE</option>
                    <option value="UEP">UEP</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Status</label>
                <select wire:model.live="filterStatus" class="border-gray-300 rounded-lg shadow-sm">
                    <option value="all">Semua Status</option>
                    <option value="CLEARED_RKA">Cleared RKA</option>
                    <option value="FINAL_APPROVED">Final Approved</option>
                    <option value="REVIEW_DINAS">Review Dinas</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-gray-800">{{ $summary['total'] }}</div>
            <div class="text-sm text-gray-500">Total</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $summary['kube'] }}</div>
            <div class="text-sm text-gray-500">KUBE</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $summary['uep'] }}</div>
            <div class="text-sm text-gray-500">UEP</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-green-700">{{ $summary['approved'] }}</div>
            <div class="text-sm text-gray-500">Disetujui</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIK</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desil</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bidang</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">RAB</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rekening</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($usulanList->items() as $index => $usulan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-3 text-gray-500">{{ $usulanList->firstItem() + $index }}</td>
                        <td class="px-3 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $usulan->isKube() ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $usulan->jenis_bantuan->value }}
                            </span>
                        </td>
                        <td class="px-3 py-3 font-medium">{{ $usulan->nama_kelompok_usaha ?? $usulan->nama_ketua_individu }}</td>
                        <td class="px-3 py-3 font-mono text-xs">{{ $usulan->nik }}</td>
                        <td class="px-3 py-3">
                            <span class="font-bold {{ ($usulan->desil ?? 0) <= 4 ? 'text-green-600' : 'text-red-600' }}">{{ $usulan->desil ?? '-' }}</span>
                            @if($usulan->is_desil_valid ?? false)
                                <span class="ml-1 text-[10px] text-green-600" title="Tervalidasi dari DTSEN"><i class="fas fa-check-circle"></i></span>
                            @endif
                        </td>
                        <td class="px-3 py-3">{{ $usulan->bidang_usaha ?? '-' }}</td>
                        <td class="px-3 py-3 text-right font-bold">{{ $usulan->total_rab ? 'Rp ' . number_format($usulan->total_rab, 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-3 font-mono text-xs">{{ $usulan->rekening?->nomor_rekening ?? '-' }}</td>
                        <td class="px-3 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $usulan->status->bgColor() }}">
                                {{ $usulan->status->label() }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-gray-500">{{ $usulan->user?->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-400">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($usulanList->hasPages())
        <div class="px-4 py-3 border-t">
            {{ $usulanList->links() }}
        </div>
        @endif
    </div>
</div>
