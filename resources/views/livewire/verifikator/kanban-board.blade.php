<div x-data="{ showSidebar: false }">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800"><i class="fas fa-check-double text-green-600"></i> Kanban Board Verifikasi</h2>
        </div>
    </x-slot>

    <div class="flex justify-end mb-4">
        <button wire:click="loadData" class="px-3 py-1.5 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 transition">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>

    {{-- Tab Toggle --}}
    <div class="flex items-center gap-2 mb-4">
        <button wire:click="$set('activeTab', 'KUBE')"
                class="px-4 py-2 rounded-lg font-medium text-sm transition
                {{ $activeTab === 'KUBE' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            <i class="fas fa-box mr-1"></i> USULAN KUBE ({{ $kubeColumns['review']->count() + $kubeColumns['revisi']->count() + $kubeColumns['cleared']->count() }})
        </button>
        <button wire:click="$set('activeTab', 'UEP')"
                class="px-4 py-2 rounded-lg font-medium text-sm transition
                {{ $activeTab === 'UEP' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            <i class="fas fa-money-bill-wave mr-1"></i> USULAN UEP ({{ $uepColumns['review']->count() + $uepColumns['revisi']->count() + $uepColumns['cleared']->count() }})
        </button>
    </div>

    {{-- Kanban Layout: Board + Detail Panel --}}
    <div class="flex gap-4" style="min-height: 70vh;">

        {{-- Kanban Board --}}
        <div class="flex-1 {{ $showDetail && $selectedUsulan ? 'hidden lg:block lg:w-1/2' : 'w-full' }}">
            @if($activeTab === 'KUBE')
                @php $columns = $kubeColumns; @endphp
            @else
                @php $columns = $uepColumns; @endphp
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 h-full">
                {{-- Column: Review Dinas --}}
                <div class="bg-gray-50 rounded-xl p-3">
                    <div class="flex items-center gap-2 mb-3 px-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <h3 class="font-bold text-sm text-gray-700">REVIEW DINAS</h3>
                        <span class="ml-auto bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $columns['review']->count() }}</span>
                    </div>
                    <div class="space-y-3 overflow-y-auto" style="max-height: 65vh;">
                        @php $reviewItems = $columns['review']->take($this->getColumnLimit('review')); @endphp
                        @forelse($reviewItems as $usulan)
                        <div wire:click="selectUsulan({{ $usulan->id }})"
                             tabindex="0" role="button" aria-label="Lihat detail usulan {{ $usulan->nama_kelompok_usaha ?? $usulan->nama_ketua_individu }}"
                             @keydown.enter="selectUsulan({{ $usulan->id }})"
                             class="bg-white rounded-lg p-3 shadow-sm border-l-4 border-blue-500 cursor-pointer hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-blue-400
                             {{ $selectedUsulanId === $usulan->id ? 'ring-2 ring-blue-500' : '' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                    {{ ($usulan->is_desil_valid ?? false) ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    @if($usulan->is_desil_valid ?? false)
                                        <i class="fas fa-check-circle mr-0.5"></i>
                                    @endif
                                    Desil {{ $usulan->desil ?? '-' }}
                                </span>
                                <span class="text-[10px] text-gray-400">#{{ $usulan->id }}</span>
                            </div>
                            <h4 class="font-medium text-sm text-gray-800 truncate">{{ $usulan->nama_kelompok_usaha ?? $usulan->nama_ketua_individu }}</h4>
                            <p class="text-xs text-gray-400 mt-1">{{ $usulan->bidang_usaha ?? '-' }}</p>
                            @if($usulan->total_rab)
                            <p class="text-xs font-bold text-gray-600 mt-1">Rp {{ number_format($usulan->total_rab, 0, ',', '.') }}</p>
                            @endif
                            <p class="text-[10px] text-gray-400 mt-1">{{ $usulan->user?->name }} @if($usulan->anggotaDewan)({{ $usulan->anggotaDewan->partai }}) @endif</p>
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-400 text-sm">
                            Tidak ada usulan
                        </div>
                        @endforelse
                        @if($columns['review']->count() > $this->getColumnLimit('review'))
                        <button wire:click="loadMoreColumn('review')" class="w-full py-2 text-xs text-blue-600 hover:text-blue-800 font-medium">
                            +{{ $columns['review']->count() - $this->getColumnLimit('review') }} lainnya
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Column: Revisi Utusan --}}
                <div class="bg-gray-50 rounded-xl p-3">
                    <div class="flex items-center gap-2 mb-3 px-2">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <h3 class="font-bold text-sm text-gray-700">REVISI UTUSAN</h3>
                        <span class="ml-auto bg-yellow-100 text-yellow-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $columns['revisi']->count() }}</span>
                    </div>
                    <div class="space-y-3 overflow-y-auto" style="max-height: 65vh;">
                        @php $revisiItems = $columns['revisi']->take($this->getColumnLimit('revisi')); @endphp
                        @forelse($revisiItems as $usulan)
                        <div wire:click="selectUsulan({{ $usulan->id }})"
                             tabindex="0" role="button" aria-label="Lihat detail usulan {{ $usulan->nama_kelompok_usaha ?? $usulan->nama_ketua_individu }}"
                             @keydown.enter="selectUsulan({{ $usulan->id }})"
                             class="bg-white rounded-lg p-3 shadow-sm border-l-4 border-yellow-500 cursor-pointer hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-yellow-400
                             {{ $selectedUsulanId === $usulan->id ? 'ring-2 ring-yellow-500' : '' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                    {{ ($usulan->is_desil_valid ?? false) ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    @if($usulan->is_desil_valid ?? false)
                                        <i class="fas fa-check-circle mr-0.5"></i>
                                    @endif
                                    Desil {{ $usulan->desil ?? '-' }}
                                </span>
                                <span class="text-[10px] text-gray-400">#{{ $usulan->id }}</span>
                            </div>
                            <h4 class="font-medium text-sm text-gray-800 truncate">{{ $usulan->nama_kelompok_usaha ?? $usulan->nama_ketua_individu }}</h4>
                            @if($usulan->catatan_verifikator)
                            <p class="text-[10px] text-red-500 mt-1 truncate" title="{{ $usulan->catatan_verifikator }}"><i class="fas fa-sticky-note"></i> {{ $usulan->catatan_verifikator }}</p>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-400 text-sm">
                            Tidak ada usulan
                        </div>
                        @endforelse
                        @if($columns['revisi']->count() > $this->getColumnLimit('revisi'))
                        <button wire:click="loadMoreColumn('revisi')" class="w-full py-2 text-xs text-yellow-600 hover:text-yellow-800 font-medium">
                            +{{ $columns['revisi']->count() - $this->getColumnLimit('revisi') }} lainnya
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Column: Cleared RKA --}}
                <div class="bg-gray-50 rounded-xl p-3">
                    <div class="flex items-center gap-2 mb-3 px-2">
                        <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                        <h3 class="font-bold text-sm text-gray-700">CLEARED RKA</h3>
                        <span class="ml-auto bg-orange-100 text-orange-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $columns['cleared']->count() }}</span>
                    </div>
                    <div class="space-y-3 overflow-y-auto" style="max-height: 65vh;">
                        @php $clearedItems = $columns['cleared']->take($this->getColumnLimit('cleared')); @endphp
                        @forelse($clearedItems as $usulan)
                        <div wire:click="selectUsulan({{ $usulan->id }})"
                             tabindex="0" role="button" aria-label="Lihat detail usulan {{ $usulan->nama_kelompok_usaha ?? $usulan->nama_ketua_individu }}"
                             @keydown.enter="selectUsulan({{ $usulan->id }})"
                             class="bg-white rounded-lg p-3 shadow-sm border-l-4 border-orange-500 cursor-pointer hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-orange-400
                             {{ $selectedUsulanId === $usulan->id ? 'ring-2 ring-orange-500' : '' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                    {{ ($usulan->is_desil_valid ?? false) ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                    @if($usulan->is_desil_valid ?? false)
                                        <i class="fas fa-check-circle mr-0.5"></i>
                                    @endif
                                    Desil {{ $usulan->desil ?? '-' }}
                                </span>
                                <span class="text-[10px] text-gray-400">#{{ $usulan->id }}</span>
                            </div>
                            <h4 class="font-medium text-sm text-gray-800 truncate">{{ $usulan->nama_kelompok_usaha ?? $usulan->nama_ketua_individu }}</h4>
                            @if($usulan->total_rab)
                            <p class="text-xs font-bold text-gray-600 mt-1">Rp {{ number_format($usulan->total_rab, 0, ',', '.') }}</p>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-400 text-sm">
                            Tidak ada usulan
                        </div>
                        @endforelse
                        @if($columns['cleared']->count() > $this->getColumnLimit('cleared'))
                        <button wire:click="loadMoreColumn('cleared')" class="w-full py-2 text-xs text-orange-600 hover:text-orange-800 font-medium">
                            +{{ $columns['cleared']->count() - $this->getColumnLimit('cleared') }} lainnya
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Panel (Split Pane) --}}
        @if($showDetail && $selectedUsulan)
        <div class="w-full lg:w-1/2 bg-white rounded-xl shadow-sm overflow-hidden flex flex-col">
            {{-- Header --}}
            <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold
                        {{ $selectedUsulan->isKube() ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ $selectedUsulan->jenis_bantuan->value }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $selectedUsulan->status->bgColor() }}">
                        {{ $selectedUsulan->status->label() }}
                    </span>
                    <span class="text-xs text-gray-400">#{{ $selectedUsulan->id }}</span>
                </div>
                <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            {{-- Content --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-4" style="max-height: 70vh;">
                {{-- Info Utama --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-400">Nama</label>
                        <p class="font-bold text-gray-800">{{ $selectedUsulan->nama_kelompok_usaha ?? $selectedUsulan->nama_ketua_individu }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400">NIK</label>
                        <p class="font-mono text-sm text-gray-800">{{ $selectedUsulan->nik }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400">Desil</label>
                        <p class="font-bold {{ $selectedUsulan->desil && $selectedUsulan->desil <= 4 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $selectedUsulan->desil ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400">Bidang Usaha</label>
                        <p class="text-gray-800">{{ $selectedUsulan->bidang_usaha ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400">No HP</label>
                        <p class="text-gray-800">{{ $selectedUsulan->no_hp ?? '-' }}</p>
                    </div>
                    @if($selectedUsulan->total_rab)
                    <div>
                        <label class="text-xs text-gray-400">Total RAB</label>
                        <p class="font-bold text-gray-800">Rp {{ number_format($selectedUsulan->total_rab, 0, ',', '.') }}</p>
                    </div>
                    @endif
                    @if($selectedUsulan->no_nib)
                    <div>
                        <label class="text-xs text-gray-400">NIB</label>
                        <p class="text-gray-800">{{ $selectedUsulan->no_nib }}</p>
                    </div>
                    @endif
                </div>

                {{-- Pengurus KUBE --}}
                @if($selectedUsulan->isKube())
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 class="font-bold text-sm text-blue-800 mb-2"><i class="fas fa-users mr-1"></i> Pengurus KUBE</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                        {{-- Ketua --}}
                        <div class="p-2 bg-white rounded border border-blue-100">
                            <div class="font-bold text-blue-700 text-xs uppercase mb-1">Ketua</div>
                            <div class="font-medium">{{ $selectedUsulan->nama_ketua_individu }}</div>
                            <div class="text-gray-500 font-mono text-xs">NIK: {{ $selectedUsulan->nik }}</div>
                            @if($selectedUsulan->no_kk_ketua)
                            <div class="text-gray-500 font-mono text-xs">KK: {{ $selectedUsulan->no_kk_ketua }}</div>
                            @endif
                            <div class="text-gray-500 text-xs">HP: {{ $selectedUsulan->no_hp ?? '-' }}</div>
                            <div class="text-xs mt-1"><span class="font-medium">Desil:</span> <span class="font-bold {{ $selectedUsulan->desil <= 4 ? 'text-green-600' : 'text-red-600' }}">{{ $selectedUsulan->desil ?? '-' }}</span></div>
                        </div>
                        {{-- Sekretaris --}}
                        @if($selectedUsulan->nama_sekretaris)
                        <div class="p-2 bg-white rounded border border-blue-100">
                            <div class="font-bold text-blue-700 text-xs uppercase mb-1">Sekretaris</div>
                            <div class="font-medium">{{ $selectedUsulan->nama_sekretaris }}</div>
                            <div class="text-gray-500 font-mono text-xs">NIK: {{ $selectedUsulan->nik_sekretaris }}</div>
                            @if($selectedUsulan->no_kk_sekretaris)
                            <div class="text-gray-500 font-mono text-xs">KK: {{ $selectedUsulan->no_kk_sekretaris }}</div>
                            @endif
                            <div class="text-gray-500 text-xs">HP: {{ $selectedUsulan->no_hp_sekretaris ?? '-' }}</div>
                            <div class="text-xs mt-1"><span class="font-medium">Desil:</span> <span class="font-bold {{ $selectedUsulan->desil_sekretaris <= 4 ? 'text-green-600' : 'text-red-600' }}">{{ $selectedUsulan->desil_sekretaris ?? '-' }}</span></div>
                        </div>
                        @endif
                        {{-- Bendahara --}}
                        @if($selectedUsulan->nama_bendahara)
                        <div class="p-2 bg-white rounded border border-blue-100">
                            <div class="font-bold text-blue-700 text-xs uppercase mb-1">Bendahara</div>
                            <div class="font-medium">{{ $selectedUsulan->nama_bendahara }}</div>
                            <div class="text-gray-500 font-mono text-xs">NIK: {{ $selectedUsulan->nik_bendahara }}</div>
                            @if($selectedUsulan->no_kk_bendahara)
                            <div class="text-gray-500 font-mono text-xs">KK: {{ $selectedUsulan->no_kk_bendahara }}</div>
                            @endif
                            <div class="text-gray-500 text-xs">HP: {{ $selectedUsulan->no_hp_bendahara ?? '-' }}</div>
                            <div class="text-xs mt-1"><span class="font-medium">Desil:</span> <span class="font-bold {{ $selectedUsulan->desil_bendahara <= 4 ? 'text-green-600' : 'text-red-600' }}">{{ $selectedUsulan->desil_bendahara ?? '-' }}</span></div>
                        </div>
                        @endif
                    </div>
                    {{-- Anggota Tambahan --}}
                    @if($selectedUsulan->anggota->count())
                    <div class="mt-2 text-xs text-blue-700">
                        <span class="font-medium">Anggota Tambahan:</span> {{ $selectedUsulan->anggota->count() }} orang
                    </div>
                    @endif
                </div>
                @endif

                {{-- Rekening (UEP) --}}
                @if($selectedUsulan->isUep() && $selectedUsulan->rekening)
                <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                    <h4 class="font-bold text-sm text-green-800 mb-2"><i class="fas fa-university mr-1"></i> Data Rekening Bank</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div><span class="text-gray-500">Bank:</span> <strong>{{ $selectedUsulan->rekening->nama_bank }}</strong></div>
                        <div><span class="text-gray-500">Rek:</span> <strong class="font-mono">{{ $selectedUsulan->rekening->nomor_rekening }}</strong></div>
                        <div class="col-span-2"><span class="text-gray-500">Atas Nama:</span> <strong>{{ $selectedUsulan->rekening->nama_pemilik_rekening }}</strong></div>
                    </div>
                </div>
                @endif

{{-- Lokasi --}}
<div class="p-3 bg-gray-50 rounded-lg">
    <h4 class="font-bold text-sm text-gray-700 mb-2"><i class="fas fa-map-marker-alt mr-1"></i> Lokasi</h4>
    <div class="text-sm text-gray-600 space-y-0.5">
        @if($selectedUsulan->kabupaten || $selectedUsulan->kecamatan || $selectedUsulan->desa_kelurahan)
        <p><span class="font-medium">Kab:</span> {{ $selectedUsulan->kabupaten ?? '-' }}, <span class="font-medium">Kec:</span> {{ $selectedUsulan->kecamatan ?? '-' }}, <span class="font-medium">Desa:</span> {{ $selectedUsulan->desa_kelurahan ?? '-' }}</p>
        @endif
        @if($selectedUsulan->alamat_detail)
        <p>{{ $selectedUsulan->alamat_detail }}</p>
        @elseif($selectedUsulan->alamat_lengkap)
        <p>{{ $selectedUsulan->alamat_lengkap }}</p>
        @endif
    </div>
                    @if($selectedUsulan->latitude && $selectedUsulan->longitude)
                    <a href="https://www.google.com/maps?q={{ $selectedUsulan->latitude }},{{ $selectedUsulan->longitude }}" target="_blank"
                       class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline mt-1">
                        <i class="fas fa-map-marker-alt"></i> Lihat di Google Maps
                    </a>
                    @endif
                    @if($selectedUsulan->link_google_maps)
                    <a href="{{ $selectedUsulan->link_google_maps }}" target="_blank"
                       class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline mt-1 ml-2">
                        <i class="fas fa-external-link-alt"></i> Link Maps
                    </a>
                    @endif
                </div>

                {{-- Dokumen --}}
                <div class="p-3 bg-blue-50 rounded-lg">
                    <h4 class="font-bold text-sm text-blue-800 mb-2"><i class="fas fa-file-alt mr-1"></i> Dokumen</h4>
                    <div class="space-y-1">
                        @forelse($selectedUsulan->dokumen as $doc)
                        <a href="{{ $doc->fileUrl() }}" target="_blank"
                           class="flex items-center gap-2 text-sm text-blue-600 hover:underline">
                            <i class="fas fa-file"></i>
                            {{ $doc->jenis_dokumen }}: {{ $doc->nama_file }}
                        </a>
                        @empty
                        <p class="text-sm text-gray-400">Tidak ada dokumen</p>
                        @endforelse
                    </div>
                </div>

                {{-- Utusan --}}
                <div class="p-3 bg-gray-50 rounded-lg">
                    <h4 class="font-bold text-sm text-gray-700 mb-1"><i class="fas fa-user-tie mr-1"></i> Utusan Dewan</h4>
                    <p class="text-sm text-gray-600">{{ $selectedUsulan->user?->name ?? '-' }} @if($selectedUsulan->anggotaDewan)({{ $selectedUsulan->anggotaDewan->partai }} — {{ $selectedUsulan->anggotaDewan->dapil }}) @endif</p>
                </div>

                {{-- Catatan --}}
                @if($selectedUsulan->catatan_verifikator)
                <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                    <h4 class="font-bold text-sm text-yellow-800 mb-1"><i class="fas fa-sticky-note mr-1"></i> Catatan Verifikator</h4>
                    <p class="text-sm text-yellow-700">{{ $selectedUsulan->catatan_verifikator }}</p>
                </div>
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="p-4 border-t bg-gray-50">
                @if($selectedUsulan->canBeVerified())
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (wajib jika Return)</label>
                    <textarea wire:model.live="catatan" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm text-sm" placeholder="Tambahkan catatan..."></textarea>
                </div>
                <div class="flex gap-2">
                    <button wire:click="returnWithNote({{ $selectedUsulan->id }})"
                            class="flex-1 px-4 py-2.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition font-medium text-sm">
                        <span wire:loading.class="hidden" wire:target="returnWithNote"><i class="fas fa-undo me-1"></i> Return (Revisi)</span>
                        <span wire:loading class="flex items-center justify-center gap-1"><i class="fas fa-spinner fa-spin"></i> Processing...</span>
                    </button>
                    <button wire:click="approve({{ $selectedUsulan->id }})"
                            class="flex-1 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm">
                        <span wire:loading.class="hidden" wire:target="approve"><i class="fas fa-check me-1"></i> Approve (Cleared RKA)</span>
                        <span wire:loading class="flex items-center justify-center gap-1"><i class="fas fa-spinner fa-spin"></i> Processing...</span>
                    </button>
                </div>
                @elseif($selectedUsulan->status === \App\Enums\UsulanStatus::CLEARED_RKA)
                <button wire:click="finalApprove({{ $selectedUsulan->id }})"
                        class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                    <span wire:loading.class="hidden" wire:target="finalApprove"><i class="fas fa-stamp me-1"></i> Final Approve</span>
                    <span wire:loading class="flex items-center justify-center gap-1"><i class="fas fa-spinner fa-spin"></i> Processing...</span>
                </button>
                @else
                <p class="text-sm text-gray-400 text-center">Tidak ada aksi yang tersedia</p>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
