<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800"><i class="fas fa-table text-blue-600"></i> Data Desil DTSEN</h2>
        </div>
    </x-slot>

    {{-- Import --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-bold text-gray-800 mb-3"><i class="fas fa-file-import mr-1"></i> Import Data DTSEN (CSV)</h3>
        <div class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm text-gray-600 mb-1">Format CSV: NIK,Nama,Desil,Alamat,Desa,Kecamatan,Kabupaten</label>
                <input type="file" wire:model.live="csvFile" accept=".csv" class="w-full border-gray-300 rounded-lg shadow-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Tahun Sync</label>
                <input type="number" wire:model.live="tahunSync" class="border-gray-300 rounded-lg shadow-sm w-32">
            </div>
            <button wire:click="importCsv" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                <span wire:loading.remove wire:target="importCsv"><i class="fas fa-upload me-1"></i> Import</span>
                <span wire:loading wire:target="importCsv"><i class="fas fa-spinner fa-spin me-1"></i> Importing...</span>
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <div class="flex flex-wrap items-center gap-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari NIK, nama, desa..."
                   class="border-gray-300 rounded-lg shadow-sm w-72">
            <select wire:model.live="filterDesil" class="border-gray-300 rounded-lg shadow-sm">
                <option value="">Semua Desil</option>
                <option value="1">Desil 1 — Sangat Miskin</option>
                <option value="2">Desil 2 — Miskin</option>
                <option value="3">Desil 3 — Rentan Miskin</option>
                <option value="4">Desil 4 — Hampir Miskin</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIK</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desil</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desa</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kecamatan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kabupaten</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($desilList as $index => $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ ($desilList->currentPage() - 1) * $desilList->perPage() + $index + 1 }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $item->nik }}</td>
                    <td class="px-4 py-3">{{ $item->nama }}</td>
                    <td class="px-4 py-3">
                        <span class="font-bold px-2 py-0.5 rounded-full text-xs
                            {{ $item->desil <= 4 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $item->desil }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $item->desa }}</td>
                    <td class="px-4 py-3">{{ $item->kecamatan }}</td>
                    <td class="px-4 py-3">{{ $item->kabupaten }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada data. Import file CSV terlebih dahulu.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $desilList->links() }}
        </div>
    </div>
</div>
