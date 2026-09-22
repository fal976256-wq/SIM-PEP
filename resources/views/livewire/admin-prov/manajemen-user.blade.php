<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800"><i class="fas fa-users-cog mr-2 text-purple-600"></i>Manajemen User</h2>
        </div>
    </x-slot>

    <div class="flex justify-end mb-4">
        <button wire:click="showCreateForm"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
            <i class="fas fa-plus me-1"></i> Tambah User
        </button>
    </div>

    {{-- Form --}}
    @if($showForm)
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-bold text-gray-800 mb-4">{{ $editId ? 'Edit' : 'Tambah' }} User</h3>
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                    <input type="text" wire:model.live="name" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" wire:model.live="email" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password {{ $editId ? '(Kosongkan jika tidak diubah)' : '*' }}</label>
                    <input type="password" wire:model.live="password" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select wire:model.live="role" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="UTUSAN_DEWAN">Utusan Dewan</option>
                        <option value="VERIFIKATOR_DINAS">Verifikator Dinas</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                    <input type="text" wire:model.live="noHp" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                @if($role === 'UTUSAN_DEWAN')
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Anggota Dewan *</label>
                    <select wire:model.live="anggotaDewanId" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">— Pilih Anggota Dewan —</option>
                        @foreach($anggotaList as $dapil => $members)
                            <optgroup label="{{ $dapil }}">
                                @foreach($members as $m)
                                    <option value="{{ $m->id }}">{{ $m->nama }} ({{ $m->partai }})</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">
                    <span wire:loading.remove wire:target="save"><i class="fas fa-save me-1"></i> Simpan</span>
                    <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...</span>
                </button>
                <button type="button" wire:click="resetForm" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
                    Batal
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Search --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, email, nama dewan..."
               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">DAPIL</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($userList as $index => $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ ($userList->currentPage() - 1) * $userList->perPage() + $index + 1 }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $user->name }}</div>
                        @if($user->anggotaDewan)
                        <div class="text-xs text-gray-400">{{ $user->anggotaDewan->nama }} — {{ $user->anggotaDewan->partai }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ match($user->role->value) {
                                'ADMIN_PROV' => 'bg-purple-100 text-purple-800',
                                'VERIFIKATOR_DINAS' => 'bg-green-100 text-green-800',
                                default => 'bg-blue-100 text-blue-800',
                            } }}">
                            {{ $user->role->label() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $user->anggotaDewan?->dapil ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <button wire:click="edit({{ $user->id }})" class="text-blue-600 hover:text-blue-800 text-xs me-2">
                            <i class="fas fa-pen me-1"></i> Edit
                        </button>
                        <button wire:click="toggleActive({{ $user->id }})"
                                wire:confirm="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} user ini?"
                                class="{{ $user->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }} text-xs">
                            <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} me-1"></i> {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">Tidak ada user</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $userList->links() }}
        </div>
    </div>
</div>
