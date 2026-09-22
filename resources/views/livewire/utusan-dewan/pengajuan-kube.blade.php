<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800"><i class="fas fa-box text-blue-600"></i> Pengajuan KUBE</h2>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span class="{{ $step >= 1 ? 'text-blue-600 font-bold' : '' }}">1. Identitas</span>
                <span>→</span>
                <span class="{{ $step >= 2 ? 'text-blue-600 font-bold' : '' }}">2. RAB</span>
                <span>→</span>
                <span class="{{ $step >= 3 ? 'text-blue-600 font-bold' : '' }}">3. Dokumen</span>
            </div>
        </div>
    </x-slot>

    {{-- Success Message --}}
    @if($successMessage)
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-center">
        <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
        <h3 class="text-base font-semibold text-green-700">{{ $successMessage }}</h3>
        <a href="{{ route('utusan.usulan-saya') }}" wire:navigate class="mt-3 inline-block text-blue-600 hover:underline">Lihat Daftar Usulan →</a>
    </div>
    @endif

    {{-- Edit Mode Banner --}}
    @if($isEditMode)
    <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-xl">
        <div class="flex items-center gap-2 mb-2">
            <i class="fas fa-edit text-amber-600"></i>
            <h3 class="text-sm font-bold text-amber-800">Mode Edit — Usulan #{{ $editId }}</h3>
        </div>
        @if($catatanVerifikator)
        <div class="bg-white rounded-lg p-3 border border-amber-100">
            <p class="text-xs font-medium text-gray-500 mb-1"><i class="fas fa-sticky-note"></i> Catatan Verifikator:</p>
            <p class="text-sm text-red-600">{{ $catatanVerifikator }}</p>
        </div>
        @endif
    </div>
    @endif

    {{-- Draft Banner --}}
    @if($showDraftBanner)
    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-blue-800">
                <i class="fas fa-save mr-1"></i> Anda memiliki draft yang belum selesai (Step {{ $draftStep }}).
            </p>
            <p class="text-xs text-blue-600 mt-0.5">
                Draft tersimpan: {{ $draftUpdatedAt?->diffForHumans() ?? '-' }}
            </p>
        </div>
        <div class="flex gap-2 ml-4 flex-shrink-0">
            <button wire:click="deleteDraft" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition">
                Mulai Baru
            </button>
            <button wire:click="restoreDraft" class="text-sm bg-blue-600 text-white px-4 py-1.5 rounded-lg hover:bg-blue-700 transition font-medium">
                Lanjutkan Draft
            </button>
        </div>
    </div>
    @endif

    {{-- Progress Bar --}}
    <div class="bg-white rounded-xl shadow-sm p-3 mb-4">
        <div class="flex items-center">
            @foreach([1 => 'Identitas & Lokasi Usaha', 2 => 'Bidang Usaha & RAB', 3 => 'Upload Dokumen'] as $s => $label)
            <div class="flex items-center {{ $s < 3 ? 'flex-1' : '' }}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                    {{ $step >= $s ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                    {{ $s }}
                </div>
                <span class="ml-2 text-sm whitespace-nowrap {{ $step >= $s ? 'text-blue-600 font-medium' : 'text-gray-400' }}">{{ $label }}</span>
                @if($s < 3)
                <div class="flex-1 mx-3">
                    <div class="h-0.5 {{ $step > $s ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Errors --}}
    @if(!empty($errors_list))
    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl">
        <div class="flex items-center gap-2 text-red-700 font-bold mb-2">
            <i class="fas fa-exclamation-triangle"></i> Terdapat kesalahan:
        </div>
        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
            @foreach($errors_list as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form wire:submit.prevent="{{ $step === 3 ? 'submit' : 'nextStep' }}">
        {{-- STEP 1 --}}
        @if($step === 1)
        <div class="space-y-4">
            {{-- Data Kelompok --}}
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-users text-blue-600"></i> Data Kelompok
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Tahun Asal Proposal *</label>
                        <select wire:model.live="tahunAsalProposal" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Pilih Tahun --</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Nama KUBE *</label>
                        <input type="text" wire:model.live="namaKube" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Masukkan nama KUBE">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Sektor Usaha *</label>
                        <select wire:model.live="sektorUsaha" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Pilih Sektor --</option>
                            @foreach($sektorUsahaList as $sektor)
                            <option value="{{ $sektor }}">{{ $sektor }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($sektorUsaha === 'Lainnya')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Sebutkan Sektor Usaha *</label>
                        <input type="text" wire:model.live="sektorUsahaLainnya" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Tuliskan sektor usaha">
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Jumlah Anggota *</label>
                        <input type="number" wire:model.live="jumlahAnggota" min="5" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Minimal 5 orang">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Left: Pengurus --}}
                <div class="space-y-4">
                    {{-- Data Ketua --}}
                    <div class="bg-white rounded-xl shadow-sm p-4" x-data="{ open: window.innerWidth >= 1024 }">
                        <button type="button" @click="open = !open" class="w-full text-base font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-user-tie text-blue-600"></i> Data Ketua
                            <i class="fas fa-chevron-down ml-auto text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-transition class="mt-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">NIK Ketua *</label>
                                <input type="text" wire:model.live="nikKetua" maxlength="16" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="16 digit NIK">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">No KK Ketua *</label>
                                <input type="text" wire:model.live="noKkKetua" maxlength="16" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="16 digit No KK">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">Nama Lengkap *</label>
                                <input type="text" wire:model.live="namaKetua" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Masukkan nama lengkap">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">No HP *</label>
                                <input type="text" wire:model.live="noHpKetua" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="08xxxxxxxxxx">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">Desil *</label>
                                <select wire:model.live="desilKetua" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Pilih Desil --</option>
                                    @foreach($desilList as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                {{-- DTSEN validation feedback --}}
                                @php $status = $this->getDesilStatus($nikKetua, $desilKetua); @endphp
                                @if($status === 'verified')
                                    <p class="mt-1 text-xs text-green-600"><i class="fas fa-check-circle mr-1"></i>Tervalidasi dari DTSEN ({{ $nikDesilCache[$nikKetua]['nama'] ?? '-' }})</p>
                                @elseif($status === 'not_found')
                                    <p class="mt-1 text-xs text-amber-600"><i class="fas fa-exclamation-triangle mr-1"></i>NIK belum terdata di DTSEN — <a href="https://cekbansos.kemensos.go.id" target="_blank" onclick="navigator.clipboard.writeText(@js($nikKetua)).then(()=>this.textContent='NIK tersalin ✓')" class="underline font-medium">Cek manual</a></p>
                                @elseif($status === 'mismatch')
                                    <p class="mt-1 text-xs text-red-600"><i class="fas fa-times-circle mr-1"></i>Desil tidak cocok (seharusnya {{ $nikDesilCache[$nikKetua]['desil'] ?? '-' }})</p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">Foto KTP *</label>
                                <input type="file" wire:model.live="fotoKtpKetua" accept="image/*" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @if($fotoKtpKetua)
                                <p class="mt-1 text-sm text-green-600"><i class="fas fa-check"></i> {{ $fotoKtpKetua->getClientOriginalName() }}</p>
                                @endif
                            </div>
                        </div>
                        </div>
                    </div>

                    {{-- Data Sekretaris --}}
                    <div class="bg-white rounded-xl shadow-sm p-4" x-data="{ open: window.innerWidth >= 1024 }">
                        <button type="button" @click="open = !open" class="w-full text-base font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-user-pen text-blue-600"></i> Data Sekretaris
                            <i class="fas fa-chevron-down ml-auto text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-transition class="mt-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">NIK Sekretaris *</label>
                                <input type="text" wire:model.live="nikSekretaris" maxlength="16" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="16 digit NIK">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">No KK Sekretaris *</label>
                                <input type="text" wire:model.live="noKkSekretaris" maxlength="16" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="16 digit No KK">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">Nama Lengkap *</label>
                                <input type="text" wire:model.live="namaSekretaris" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Masukkan nama lengkap">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">No HP *</label>
                                <input type="text" wire:model.live="noHpSekretaris" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="08xxxxxxxxxx">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">Desil *</label>
                                <select wire:model.live="desilSekretaris" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Pilih Desil --</option>
                                    @foreach($desilList as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @php $status = $this->getDesilStatus($nikSekretaris, $desilSekretaris); @endphp
                                @if($status === 'verified')
                                    <p class="mt-1 text-xs text-green-600"><i class="fas fa-check-circle mr-1"></i>Tervalidasi dari DTSEN ({{ $nikDesilCache[$nikSekretaris]['nama'] ?? '-' }})</p>
                                @elseif($status === 'not_found')
                                    <p class="mt-1 text-xs text-amber-600"><i class="fas fa-exclamation-triangle mr-1"></i>NIK belum terdata di DTSEN — <a href="https://cekbansos.kemensos.go.id" target="_blank" onclick="navigator.clipboard.writeText(@js($nikSekretaris)).then(()=>this.textContent='NIK tersalin ✓')" class="underline font-medium">Cek manual</a></p>
                                @elseif($status === 'mismatch')
                                    <p class="mt-1 text-xs text-red-600"><i class="fas fa-times-circle mr-1"></i>Desil tidak cocok (seharusnya {{ $nikDesilCache[$nikSekretaris]['desil'] ?? '-' }})</p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">Foto KTP *</label>
                                <input type="file" wire:model.live="fotoKtpSekretaris" accept="image/*" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @if($fotoKtpSekretaris)
                                <p class="mt-1 text-sm text-green-600"><i class="fas fa-check"></i> {{ $fotoKtpSekretaris->getClientOriginalName() }}</p>
                                @endif
                            </div>
                        </div>
                        </div>
                    </div>

                    {{-- Data Bendahara --}}
                    <div class="bg-white rounded-xl shadow-sm p-4" x-data="{ open: window.innerWidth >= 1024 }">
                        <button type="button" @click="open = !open" class="w-full text-base font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-money-check text-blue-600"></i> Data Bendahara
                            <i class="fas fa-chevron-down ml-auto text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-transition class="mt-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">NIK Bendahara *</label>
                                <input type="text" wire:model.live="nikBendahara" maxlength="16" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="16 digit NIK">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">No KK Bendahara *</label>
                                <input type="text" wire:model.live="noKkBendahara" maxlength="16" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="16 digit No KK">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">Nama Lengkap *</label>
                                <input type="text" wire:model.live="namaBendahara" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Masukkan nama lengkap">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">No HP *</label>
                                <input type="text" wire:model.live="noHpBendahara" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="08xxxxxxxxxx">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">Desil *</label>
                                <select wire:model.live="desilBendahara" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Pilih Desil --</option>
                                    @foreach($desilList as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @php $status = $this->getDesilStatus($nikBendahara, $desilBendahara); @endphp
                                @if($status === 'verified')
                                    <p class="mt-1 text-xs text-green-600"><i class="fas fa-check-circle mr-1"></i>Tervalidasi dari DTSEN ({{ $nikDesilCache[$nikBendahara]['nama'] ?? '-' }})</p>
                                @elseif($status === 'not_found')
                                    <p class="mt-1 text-xs text-amber-600"><i class="fas fa-exclamation-triangle mr-1"></i>NIK belum terdata di DTSEN — <a href="https://cekbansos.kemensos.go.id" target="_blank" onclick="navigator.clipboard.writeText(@js($nikBendahara)).then(()=>this.textContent='NIK tersalin ✓')" class="underline font-medium">Cek manual</a></p>
                                @elseif($status === 'mismatch')
                                    <p class="mt-1 text-xs text-red-600"><i class="fas fa-times-circle mr-1"></i>Desil tidak cocok (seharusnya {{ $nikDesilCache[$nikBendahara]['desil'] ?? '-' }})</p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">Foto KTP *</label>
                                <input type="file" wire:model.live="fotoKtpBendahara" accept="image/*" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @if($fotoKtpBendahara)
                                <p class="mt-1 text-sm text-green-600"><i class="fas fa-check"></i> {{ $fotoKtpBendahara->getClientOriginalName() }}</p>
                                @endif
                            </div>
                        </div>
                        </div>
                    </div>

                    {{-- Anggota Tambahan --}}
                    <div class="bg-white rounded-xl shadow-sm p-4" x-data="{ open: window.innerWidth >= 1024 }">
                        <div class="flex items-center justify-between">
                            <button type="button" @click="open = !open" class="flex-1 text-base font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-user-group text-blue-600"></i> Anggota Tambahan
                                <span class="text-xs font-normal text-gray-400">(Minimal 1 orang)</span>
                                <i class="fas fa-chevron-down ml-auto text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <button type="button" wire:click="addAnggota" class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-medium hover:bg-blue-200 transition ml-2">
                                <i class="fas fa-plus mr-1"></i> Tambah
                            </button>
                        </div>
                        <div x-show="open" x-transition class="mt-3">

                        @if(empty($anggota))
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada anggota tambahan. Klik "Tambah" untuk menambahkan.</p>
                        @else
                        <div class="space-y-4">
                            @foreach($anggota as $i => $ang)
                            <div wire:key="anggota-{{ $i }}" class="p-3 bg-gray-50 rounded-lg border border-gray-200 relative">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-bold text-gray-600">Anggota {{ $i + 1 }}</span>
                                    <button type="button" wire:click="removeAnggota({{ $i }})" class="text-red-400 hover:text-red-600 text-sm">
                                        <i class="fas fa-times-circle"></i> Hapus
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-0.5">NIK *</label>
                                        <input type="text" wire:model.live="anggota.{{ $i }}.nik" maxlength="16" class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="16 digit NIK">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-0.5">No KK *</label>
                                        <input type="text" wire:model.live="anggota.{{ $i }}.no_kk" maxlength="16" class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="16 digit No KK">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Nama Lengkap *</label>
                                        <input type="text" wire:model.live="anggota.{{ $i }}.nama" class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-0.5">No HP *</label>
                                        <input type="text" wire:model.live="anggota.{{ $i }}.no_hp" class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="08xxxxxxxxxx">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Desil *</label>
                                        <select wire:model.live="anggota.{{ $i }}.desil" class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">-- Pilih Desil --</option>
                                            @foreach($desilList as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @php $angNik = $ang['nik'] ?? ''; $angDesil = $ang['desil'] ?? ''; $angStatus = $this->getDesilStatus($angNik, $angDesil); @endphp
                                        @if($angStatus === 'verified')
                                            <p class="mt-0.5 text-[10px] text-green-600"><i class="fas fa-check-circle mr-0.5"></i>DTSEN OK</p>
                                        @elseif($angStatus === 'not_found')
                                            <p class="mt-0.5 text-[10px] text-amber-600"><i class="fas fa-exclamation-triangle mr-0.5"></i>Belum terdata — <a href="https://cekbansos.kemensos.go.id" target="_blank" onclick="navigator.clipboard.writeText(@js($ang['nik'] ?? '')).then(()=>this.textContent='Tersalin ✓')" class="underline">Cek manual</a></p>
                                        @elseif($angStatus === 'mismatch')
                                            <p class="mt-0.5 text-[10px] text-red-600"><i class="fas fa-times-circle mr-0.5"></i>Cocokkan DTSEN</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        </div>
                    </div>
                </div>

                {{-- Right: Lokasi --}}
                <div>
                    <div class="bg-white rounded-xl shadow-sm p-4 sticky top-4">
                        <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-blue-600"></i> Lokasi Usaha
                        </h3>

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-0.5">Kabupaten *</label>
                                    <select wire:model.live="kabupaten" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">-- Pilih Kabupaten --</option>
                                        @foreach($kabupatenList as $kab)
                                        <option value="{{ $kab }}">{{ $kab }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-0.5">Kecamatan *</label>
                                    <select wire:model.live="kecamatan" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" {{ empty($kecamatanList) ? 'disabled' : '' }}>
                                        <option value="">-- Pilih Kecamatan --</option>
                                        @foreach($kecamatanList as $kec)
                                        <option value="{{ $kec }}">{{ $kec }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">Desa/Kelurahan *</label>
                                <input type="text" wire:model.live="desaKelurahan" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Masukkan nama desa">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">Alamat Detail *</label>
                                <input type="text" wire:model.live="alamatDetail" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Contoh: Jl. Pahlawan No. 12, dekat Masjid Al-Ikhlas">
                                <p class="text-xs text-gray-400 mt-0.5">Berikan contoh dekat bangunan/titik acuan agar mudah dikunjungi</p>
                            </div>

                            <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <label class="block text-sm font-medium text-blue-700 mb-1"><i class="fas fa-map-pin mr-1"></i> Pin GPS *</label>
                                <p class="text-xs text-blue-600 mb-2">Buka Google Maps, cari lokasi, klik kanan → "What's here?" → Salin koordinat (lat,lng)</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-0.5">Latitude</label>
                                        <input type="text" wire:model.live="latitude" class="w-full border-gray-300 rounded-lg shadow-sm text-sm" placeholder="-5.1234567">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-0.5">Longitude</label>
                                        <input type="text" wire:model.live="longitude" class="w-full border-gray-300 rounded-lg shadow-sm text-sm" placeholder="119.1234567">
                                    </div>
                                </div>
                                <a href="https://www.google.com/maps?q={{ $latitude }},{{ $longitude }}" target="_blank"
                                   class="mt-2 flex items-center justify-center gap-2 w-full py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                    <i class="fas fa-map-marker-alt"></i> Buka di Google Maps
                                </a>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">Link Google Maps (Opsional)</label>
                                <input type="url" wire:model.live="linkGoogleMaps" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="https://maps.google.com/...">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-0.5">Foto Tampak Depan Lokasi Usaha *</label>
                                <input type="file" wire:model.live="fotoLokasi" accept="image/*" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @if($fotoLokasi)
                                <p class="mt-1 text-sm text-green-600"><i class="fas fa-check"></i> {{ $fotoLokasi->getClientOriginalName() }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- STEP 2 --}}
        @if($step === 2)
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-calculator text-blue-600"></i> Bidang Usaha & RAB
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Bidang Usaha *</label>
                        <select wire:model.live="bidangUsaha" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Pilih Bidang Usaha --</option>
                            @foreach($bidangUsahaList as $bidang)
                            <option value="{{ $bidang }}">{{ $bidang }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($bidangUsaha === 'Lainnya')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Sebutkan Bidang Usaha *</label>
                        <input type="text" wire:model.live="bidangUsahaLainnya" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Tuliskan bidang usaha">
                    </div>
                    @endif

                    @if($bidangUsaha)
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center gap-2 text-yellow-700">
                            <i class="fas fa-info-circle"></i>
                            <span class="font-medium">Pagu Maksimal untuk "{{ $bidangUsaha === 'Lainnya' ? $bidangUsahaLainnya : $bidangUsaha }}"</span>
                        </div>
                        @if($paguMaksimal > 0)
                        <div class="text-lg font-bold text-yellow-800 mt-1">Rp {{ number_format($paguMaksimal, 0, ',', '.') }}</div>
                        @else
                        <div class="text-sm text-yellow-600 mt-1">Pagu belum diatur untuk bidang ini. Hubungi admin.</div>
                        @endif
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Total RAB (Rp) *</label>
                        <input type="number" wire:model.live="totalRab" min="0" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base" placeholder="Masukkan total RAB">
                        @if($paguMessage)
                        <p class="mt-1 text-sm {{ str_contains($paguMessage, 'melebihi') ? 'text-red-600' : 'text-green-600' }}">
                            <i class="fas {{ str_contains($paguMessage, 'melebihi') ? 'fa-times-circle' : 'fa-check-circle' }}"></i> {{ $paguMessage }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- STEP 3 --}}
        @if($step === 3)
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-file-upload text-blue-600"></i> Upload Dokumen (Wajib)
                </h3>

                <div class="space-y-4">
                    {{-- Proposal PDF --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5"><i class="fas fa-file-pdf mr-1"></i> Proposal PDF *</label>
                        @if($isEditMode && isset($existingFiles['Proposal']) && !$proposalPdf)
                        <div class="flex items-center gap-2 p-2 bg-green-50 border border-green-200 rounded-lg mb-1">
                            <i class="fas fa-file-pdf text-green-600"></i>
                            <a href="{{ $existingFiles['Proposal']['url'] }}" target="_blank" class="text-sm text-blue-600 hover:underline truncate">{{ $existingFiles['Proposal']['name'] }}</a>
                            <span class="text-xs text-green-600 ml-auto flex-shrink-0"><i class="fas fa-check"></i> Ada</span>
                        </div>
                        @endif
                        <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed {{ $proposalPdf ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50 hover:border-blue-400 hover:bg-blue-50' }} rounded-xl cursor-pointer transition">
                            @if($proposalPdf)
                                <div class="flex items-center gap-2 text-green-600">
                                    <i class="fas fa-check-circle text-2xl"></i>
                                    <span class="text-sm font-medium">{{ $proposalPdf->getClientOriginalName() }}</span>
                                </div>
                                <span class="text-xs text-gray-400 mt-1">Klik untuk ganti file</span>
                            @else
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                                <span class="text-sm text-gray-500 mt-1">Seret & lepas atau klik untuk upload</span>
                                <span class="text-xs text-gray-400">PDF, maks 5MB</span>
                            @endif
                            <input type="file" wire:model.live="proposalPdf" accept=".pdf" class="sr-only">
                        </label>
                    </div>

                    {{-- Surat Legalitas --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5"><i class="fas fa-clipboard mr-1"></i> Surat Legalitas Kelompok / SK Desa *</label>
                        @if($isEditMode && isset($existingFiles['Legalitas']) && !$legalitasPdf)
                        <div class="flex items-center gap-2 p-2 bg-green-50 border border-green-200 rounded-lg mb-1">
                            <i class="fas fa-file-alt text-green-600"></i>
                            <a href="{{ $existingFiles['Legalitas']['url'] }}" target="_blank" class="text-sm text-blue-600 hover:underline truncate">{{ $existingFiles['Legalitas']['name'] }}</a>
                            <span class="text-xs text-green-600 ml-auto flex-shrink-0"><i class="fas fa-check"></i> Ada</span>
                        </div>
                        @endif
                        <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed {{ $legalitasPdf ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50 hover:border-blue-400 hover:bg-blue-50' }} rounded-xl cursor-pointer transition">
                            @if($legalitasPdf)
                                <div class="flex items-center gap-2 text-green-600">
                                    <i class="fas fa-check-circle text-2xl"></i>
                                    <span class="text-sm font-medium">{{ $legalitasPdf->getClientOriginalName() }}</span>
                                </div>
                                <span class="text-xs text-gray-400 mt-1">Klik untuk ganti file</span>
                            @else
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                                <span class="text-sm text-gray-500 mt-1">Seret & lepas atau klik untuk upload</span>
                                <span class="text-xs text-gray-400">PDF, JPG, PNG, maks 5MB</span>
                            @endif
                            <input type="file" wire:model.live="legalitasPdf" accept=".pdf,.jpg,.jpeg,.png" class="sr-only">
                        </label>
                    </div>

                    {{-- Foto Buku Rekening --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5"><i class="fas fa-university mr-1"></i> Foto Buku Rekening Kelompok *</label>
                        @if($isEditMode && isset($existingFiles['Buku Rekening']) && !$bukuRekening)
                        <div class="flex items-center gap-2 p-2 bg-green-50 border border-green-200 rounded-lg mb-1">
                            <i class="fas fa-university text-green-600"></i>
                            <a href="{{ $existingFiles['Buku Rekening']['url'] }}" target="_blank" class="text-sm text-blue-600 hover:underline truncate">{{ $existingFiles['Buku Rekening']['name'] }}</a>
                            <span class="text-xs text-green-600 ml-auto flex-shrink-0"><i class="fas fa-check"></i> Ada</span>
                        </div>
                        @endif
                        <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed {{ $bukuRekening ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50 hover:border-blue-400 hover:bg-blue-50' }} rounded-xl cursor-pointer transition">
                            @if($bukuRekening)
                                <div class="flex items-center gap-2 text-green-600">
                                    <i class="fas fa-check-circle text-2xl"></i>
                                    <span class="text-sm font-medium">{{ $bukuRekening->getClientOriginalName() }}</span>
                                </div>
                                <span class="text-xs text-gray-400 mt-1">Klik untuk ganti file</span>
                            @else
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                                <span class="text-sm text-gray-500 mt-1">Seret & lepas atau klik untuk upload</span>
                                <span class="text-xs text-gray-400">JPG, PNG, maks 5MB</span>
                            @endif
                            <input type="file" wire:model.live="bukuRekening" accept="image/*" class="sr-only">
                        </label>
                    </div>

                    {{-- Dokumentasi Foto Kegiatan Usaha --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">
                            <i class="fas fa-camera mr-1"></i> Dokumentasi Foto Kegiatan Usaha *
                            <span class="text-xs text-gray-400 font-normal">(3–5 foto, maks 5MB per foto)</span>
                        </label>

                        {{-- Dropzone --}}
                        <label class="flex flex-col items-center justify-center w-full {{ count($dokumentasiUsaha) > 0 ? 'h-auto py-3' : 'h-20' }} border-2 border-dashed {{ count($dokumentasiUsaha) > 0 ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50 hover:border-blue-400 hover:bg-blue-50' }} rounded-xl cursor-pointer transition">
                            @if(count($dokumentasiUsaha) > 0)
                                {{-- Thumbnail grid --}}
                                <div class="grid grid-cols-5 gap-2 mb-2 w-full px-3">
                                    @foreach($dokumentasiUsaha as $i => $foto)
                                        <div wire:key="foto-{{ $i }}" class="relative group">
                                            <img src="{{ $foto->temporaryUrl() }}" alt="Foto {{ $i + 1 }}" class="w-full h-16 object-cover rounded-lg border border-gray-200">
                                            <button type="button" wire:click="removeFoto({{ $i }})"
                                                class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center opacity-80 group-hover:opacity-100 transition shadow">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <span class="absolute bottom-0 left-0 right-0 text-center text-[10px] text-white bg-black/40 rounded-b-lg py-0.5">{{ $i + 1 }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <span class="text-xs text-green-600 font-medium">{{ count($dokumentasiUsaha) }} foto dipilih — klik untuk tambah lagi</span>
                            @else
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                                <span class="text-sm text-gray-500 mt-1">Seret & lepas atau klik untuk upload</span>
                                <span class="text-xs text-gray-400">Minimal 3 foto, maks 5 foto (JPG/PNG, maks 5MB per foto)</span>
                            @endif
                            <input type="file" wire:model.live="dokumentasiUsaha" accept="image/*" multiple class="sr-only">
                        </label>

                        {{-- Validation message --}}
                        @if(count($dokumentasiUsaha) > 0 && count($dokumentasiUsaha) < 3)
                            <p class="text-xs text-amber-600 mt-1"><i class="fas fa-exclamation-triangle mr-1"></i> Minimal 3 foto wajib diupload (saat ini {{ count($dokumentasiUsaha) }} foto)</p>
                        @endif
                        @if(count($dokumentasiUsaha) > 5)
                            <p class="text-xs text-red-600 mt-1"><i class="fas fa-times-circle mr-1"></i> Maksimal 5 foto (saat ini {{ count($dokumentasiUsaha) }} foto)</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Navigation Buttons --}}
        <div class="flex justify-between mt-4">
            <div>
                @if($step > 1)
                <button type="button" wire:click="prevStep" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm">
                    ← Sebelumnya
                </button>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="saveDraft" class="px-4 py-2 border border-blue-300 text-blue-600 rounded-lg hover:bg-blue-50 transition text-sm font-medium">
                    <span wire:loading.class="hidden" wire:target="saveDraft"><i class="fas fa-save mr-1"></i> Simpan Sementara</span>
                    <span wire:loading class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Menyimpan...</span>
                </button>
                @if($step < 3)
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                    <span wire:loading.class="hidden" wire:target="nextStep">Selanjutnya →</span>
                    <span wire:loading class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Memproses...</span>
                </button>
                @else
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm">
                    <span wire:loading.class="hidden" wire:target="submit"><i class="fas fa-paper-plane me-2"></i> Submit Usulan</span>
                    <span wire:loading class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Mengirim...</span>
                </button>
                @endif
            </div>
        </div>
    </form>
</div>
