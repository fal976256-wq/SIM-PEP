<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800"><i class="fas fa-coins text-green-600"></i> Master Pagu KUBE</h2>
        </div>
    </x-slot>

    <div class="flex justify-end mb-4">
        <button wire:click="showCreateForm"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
            <i class="fas fa-plus me-1"></i> Tambah Pagu
        </button>
    </div>

    {{-- Form --}}
    @if($showForm)
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-bold text-gray-800 mb-4">{{ $editId ? 'Edit' : 'Tambah' }} Pagu</h3>
        <form wire:submit.prevent="save" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Anggaran</label>
                <input type="number" wire:model.live="tahunAnggaran" class="border-gray-300 rounded-lg shadow-sm w-32" disabled>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bidang Usaha *</label>
                <select wire:model.live="bidangUsaha" class="border-gray-300 rounded-lg shadow-sm w-64">
                    <option value="">-- Pilih --</option>
                    @foreach($bidangUsahaList as $bidang)
                    <option value="{{ $bidang }}">{{ $bidang }}</option>
                    @endforeach
                </select>
            </div>
            @if($bidangUsaha === 'Lainnya')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sebutkan Bidang Usaha *</label>
                <input type="text" wire:model.live="bidangUsahaLainnya" class="border-gray-300 rounded-lg shadow-sm w-64" placeholder="Tuliskan bidang usaha">
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pagu Maksimal (Rp) *</label>
                <input type="number" wire:model.live="paguMaksimal" min="0" class="border-gray-300 rounded-lg shadow-sm w-48" placeholder="0">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                    <span wire:loading.remove wire:target="save"><i class="fas fa-save me-1"></i> Simpan</span>
                    <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...</span>
                </button>
                <button type="button" wire:click="$set('showForm', false)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
                    Batal
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bidang Usaha</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pagu Maksimal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($paguList as $index => $pagu)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                    <td class="px-4 py-3">{{ $pagu->tahun_anggaran }}</td>
                    <td class="px-4 py-3 font-medium">{{ $pagu->bidang_usaha }}</td>
                    <td class="px-4 py-3 font-bold text-green-700">Rp {{ number_format($pagu->pagu_maksimal, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <button wire:click="edit({{ $pagu->id }})" class="text-blue-600 hover:text-blue-800 text-xs me-2"><i class="fas fa-edit"></i> Edit</button>
                        <button wire:click="delete({{ $pagu->id }})" wire:confirm="Hapus pagu ini?"
                                class="text-red-600 hover:text-red-800 text-xs"><i class="fas fa-trash"></i> Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada data pagu</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
