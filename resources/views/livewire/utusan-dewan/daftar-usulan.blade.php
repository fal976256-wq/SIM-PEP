<div>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800"><i class="fas fa-clipboard-list text-blue-600"></i> Daftar Usulan Saya</h2>
    </x-slot>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, NIK..."
                       class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <select wire:model.live="filterJenis" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="all">Semua Jenis</option>
                    <option value="KUBE">KUBE</option>
                    <option value="UEP">UEP</option>
                </select>
            </div>
            <div>
                <select wire:model.live="tahunPengajuan" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    @foreach($tahunList as $tahun)
                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ml-auto">
                <button wire:click="exportCsv" class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-medium hover:bg-green-700 transition">
                    <span wire:loading.remove wire:target="exportCsv"><i class="fas fa-download mr-1"></i> Export CSV</span>
                    <span wire:loading wire:target="exportCsv"><i class="fas fa-spinner fa-spin mr-1"></i> Exporting...</span>
                </button>
            </div>
        </div>

        {{-- Status Tabs --}}
        <div class="flex flex-wrap gap-2 mt-3">
            @php
            $tabs = [
                'all' => 'Semua',
                'DRAFT' => 'Draft',
                'REVIEW_DINAS' => 'Review Dinas',
                'REVISI_UTUSAN' => 'Revisi',
                'CLEARED_RKA' => 'Cleared RKA',
                'FINAL_APPROVED' => 'Disetujui',
            ];
            @endphp
            @foreach($tabs as $key => $label)
            <button wire:click="set('filterStatus', '{{ $key }}')"
                    class="px-3 py-1.5 rounded-full text-xs font-medium transition
                    {{ $filterStatus === $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $label }}
                @if(isset($statusCounts[$key]))
                <span class="ml-1">({{ $statusCounts[$key] }})</span>
                @endif
            </button>
            @endforeach
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIK</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desil</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($usulanList->items() as $index => $usulan)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ $usulanList->firstItem() + $index }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $usulan->nama_kelompok_usaha ?? $usulan->nama_ketua_individu }}</div>
                        <div class="text-xs text-gray-400">{{ $usulan->bidang_usaha ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $usulan->isKube() ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ $usulan->jenis_bantuan->value }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $usulan->nik }}</td>
                    <td class="px-4 py-3">
                        <span class="font-bold {{ $usulan->desil && $usulan->desil <= 4 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $usulan->desil ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $usulan->status->bgColor() }}">
                            {{ $usulan->status->label() }}
                        </span>
                        @if($usulan->catatan_verifikator && $usulan->status === \App\Enums\UsulanStatus::REVISI_UTUSAN)
                        <p class="text-xs text-red-500 mt-1 max-w-xs truncate" title="{{ $usulan->catatan_verifikator }}">
                            <i class="fas fa-sticky-note"></i> {{ $usulan->catatan_verifikator }}
                        </p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($usulan->status === \App\Enums\UsulanStatus::REVISI_UTUSAN)
                            @if($usulan->jenis_bantuan === \App\Enums\JenisBantuan::KUBE)
                            <a href="{{ route('utusan.pengajuan.kube') }}?edit={{ $usulan->id }}" wire:navigate
                               class="text-amber-600 hover:text-amber-800 text-xs font-medium mr-2">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @else
                            <a href="{{ route('utusan.pengajuan.uep') }}?edit={{ $usulan->id }}" wire:navigate
                               class="text-amber-600 hover:text-amber-800 text-xs font-medium mr-2">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @endif
                            <button wire:click="submitUsulan({{ $usulan->id }})"
                                    wire:confirm="Submit usulan ini untuk review?"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                <i class="fas fa-paper-plane"></i> Submit
                            </button>
                        @elseif($usulan->canEdit())
                        <button wire:click="submitUsulan({{ $usulan->id }})"
                                wire:confirm="Submit usulan ini untuk review?"
                                class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                            <i class="fas fa-paper-plane"></i> Submit
                        </button>
                        @else
                        <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center">
                        <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-400">Belum ada usulan</p>
                        <div class="mt-2 flex items-center justify-center gap-3">
                            <a href="{{ route('utusan.pengajuan.kube') }}" wire:navigate class="inline-block text-blue-600 hover:underline text-sm">
                                Mulai Pengajuan KUBE →
                            </a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('utusan.pengajuan.uep') }}" wire:navigate class="inline-block text-green-600 hover:underline text-sm">
                                Mulai Pengajuan UEP →
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        {{-- Pagination --}}
        @if($usulanList->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $usulanList->links() }}
        </div>
        @endif
    </div>
</div>
