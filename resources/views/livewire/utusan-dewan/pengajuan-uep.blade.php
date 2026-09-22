<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800"><i class="fas fa-money-bill-wave text-green-600"></i> Pengajuan UEP</h2>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span class="{{ $step >= 1 ? 'text-green-600 font-bold' : '' }}">1. Identitas</span>
                <span>→</span>
                <span class="{{ $step >= 2 ? 'text-green-600 font-bold' : '' }}">2. Bidang & NIB</span>
                <span>→</span>
                <span class="{{ $step >= 3 ? 'text-green-600 font-bold' : '' }}">3. SKU & Legalitas</span>
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
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-green-800">
                <i class="fas fa-save mr-1"></i> Anda memiliki draft yang belum selesai (Step {{ $draftStep }}).
            </p>
            <p class="text-xs text-green-600 mt-0.5">
                Draft tersimpan: {{ $draftUpdatedAt?->diffForHumans() ?? '-' }}
            </p>
        </div>
        <div class="flex gap-2 ml-4 flex-shrink-0">
            <button wire:click="deleteDraft" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition">
                Mulai Baru
            </button>
            <button wire:click="restoreDraft" class="text-sm bg-green-600 text-white px-4 py-1.5 rounded-lg hover:bg-green-700 transition font-medium">
                Lanjutkan Draft
            </button>
        </div>
    </div>
    @endif

    {{-- Progress Bar --}}
    <div class="bg-white rounded-xl shadow-sm p-3 mb-4">
        <div class="flex items-center">
            @foreach([1 => 'Identitas & Rekening', 2 => 'Bidang & NIB', 3 => 'SKU & Legalitas'] as $s => $label)
            <div class="flex items-center {{ $s < 3 ? 'flex-1' : '' }}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                    {{ $step >= $s ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                    {{ $s }}
                </div>
                <span class="ml-2 text-sm whitespace-nowrap {{ $step >= $s ? 'text-green-600 font-medium' : 'text-gray-400' }}">{{ $label }}</span>
                @if($s < 3)
                <div class="flex-1 mx-3">
                    <div class="h-0.5 {{ $step > $s ? 'bg-green-600' : 'bg-gray-200' }}"></div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Errors --}}
    @if(!empty($errors_list))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
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
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Identitas --}}
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-user text-green-600"></i> Data Individu
                </h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Tahun Asal Proposal *</label>
                        <select wire:model.live="tahunAsalProposal" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">-- Pilih Tahun --</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Nama Lengkap *</label>
                        <input type="text" wire:model.live="nama" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Masukkan nama lengkap">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">NIK *</label>
                        <input type="text" wire:model.live="nik" maxlength="16" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="16 digit NIK">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Desil *</label>
                        <select wire:model.live="desil" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">-- Pilih Desil --</option>
                            @foreach($desilList as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        {{-- DTSEN validation feedback --}}
                        @php $status = $this->getDesilStatus($nik, $desil); @endphp
                        @if($status === 'verified')
                            <p class="mt-1 text-xs text-green-600"><i class="fas fa-check-circle mr-1"></i>Tervalidasi dari DTSEN ({{ $nikDesilCache[$nik]['nama'] ?? '-' }})</p>
                        @elseif($status === 'not_found')
                            <p class="mt-1 text-xs text-amber-600"><i class="fas fa-exclamation-triangle mr-1"></i>NIK belum terdata di DTSEN — <a href="https://cekbansos.kemensos.go.id" target="_blank" onclick="navigator.clipboard.writeText(@js($nik)).then(()=>this.textContent='NIK tersalin ✓')" class="underline font-medium">Cek manual</a></p>
                        @elseif($status === 'mismatch')
                            <p class="mt-1 text-xs text-red-600"><i class="fas fa-times-circle mr-1"></i>Desil tidak cocok (seharusnya {{ $nikDesilCache[$nik]['desil'] ?? '-' }})</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">No HP *</label>
                        <input type="text" wire:model.live="noHp" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="08xxxxxxxxxx">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Nama Usaha (Opsional)</label>
                        <input type="text" wire:model.live="namaUsaha" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Contoh: Toko Berkah">
                    </div>

                    <div>
                        <div class="p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                            <i class="fas fa-info-circle mr-1"></i> Foto KTP akan di-upload pada Step 3 (Legalitas).
                        </div>
                    </div>
                </div>

                <h3 class="text-base font-semibold text-gray-800 mt-4 mb-3 flex items-center gap-2">
                    <i class="fas fa-university text-green-600"></i> Data Rekening Bank
                </h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Nama Bank *</label>
                        <select wire:model.live="namaBank" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">-- Pilih Bank --</option>
                            @foreach($bankList as $bank)
                            <option value="{{ $bank }}">{{ $bank }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($namaBank === 'Lainnya')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Sebutkan Nama Bank *</label>
                        <input type="text" wire:model.live="namaBankLainnya" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Tuliskan nama bank">
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Nomor Rekening *</label>
                        <input type="text" wire:model.live="nomorRekening" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Nama Pemilik Rekening *</label>
                        <input type="text" wire:model.live="namaPemilikRekening" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                        @if($rekeningMessage)
                        <p class="mt-1 text-sm {{ $rekeningValid ? 'text-green-600' : 'text-red-600' }}">
                            <i class="fas {{ $rekeningValid ? 'fa-check-circle' : 'fa-times-circle' }}"></i> {{ $rekeningMessage }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Lokasi --}}
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-green-600"></i> Lokasi Usaha
                </h3>

                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-0.5">Kabupaten *</label>
                            <select wire:model.live="kabupaten" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                <option value="">-- Pilih Kabupaten --</option>
                                @foreach($kabupatenList as $kab)
                                <option value="{{ $kab }}">{{ $kab }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-0.5">Kecamatan *</label>
                            <select wire:model.live="kecamatan" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" {{ empty($kecamatanList) ? 'disabled' : '' }}>
                                <option value="">-- Pilih Kecamatan --</option>
                                @foreach($kecamatanList as $kec)
                                <option value="{{ $kec }}">{{ $kec }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Desa/Kelurahan *</label>
                        <input type="text" wire:model.live="desaKelurahan" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Masukkan nama desa">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Alamat Detail *</label>
                        <input type="text" wire:model.live="alamatDetail" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Contoh: Jl. Pahlawan No. 12, dekat Masjid Al-Ikhlas">
                        <p class="text-xs text-gray-400 mt-0.5">Berikan contoh dekat bangunan/titik acuan agar mudah dikunjungi</p>
                    </div>

                    <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                        <label class="block text-sm font-medium text-green-700 mb-1"><i class="fas fa-map-pin mr-1"></i> Pin GPS *</label>
                        <p class="text-xs text-green-600 mb-2">Buka Google Maps → cari lokasi → klik kanan → "What's here?" → Salin koordinat</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Latitude</label>
                                <input type="text" wire:model.live="latitude" class="w-full border-gray-300 rounded-lg shadow-sm text-sm" placeholder="-5.1234567">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Longitude</label>
                                <input type="text" wire:model.live="longitude" class="w-full border-gray-300 rounded-lg shadow-sm text-sm" placeholder="119.1234567">
                            </div>
                        </div>
                        <a href="https://www.google.com/maps?q={{ $latitude }},{{ $longitude }}" target="_blank"
                           class="mt-2 flex items-center justify-center gap-2 w-full py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">
                            <i class="fas fa-map-marker-alt"></i> Buka di Google Maps
                        </a>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Link Google Maps (Opsional)</label>
                        <input type="url" wire:model.live="linkGoogleMaps" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Foto Usaha Sedang Berjalan *</label>
                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed {{ $fotoUsaha ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50 hover:border-green-400 hover:bg-green-50' }} rounded-xl cursor-pointer transition">
                            @if($fotoUsaha)
                                <div class="flex items-center gap-2 text-green-600">
                                    <i class="fas fa-check-circle text-xl"></i>
                                    <span class="text-sm font-medium">{{ $fotoUsaha->getClientOriginalName() }}</span>
                                </div>
                            @else
                                <i class="fas fa-cloud-upload-alt text-xl text-gray-400"></i>
                                <span class="text-sm text-gray-500 mt-1">Klik untuk upload foto usaha</span>
                                <span class="text-xs text-gray-400">JPG, PNG</span>
                            @endif
                            <input type="file" wire:model.live="fotoUsaha" accept="image/*" class="sr-only">
                        </label>
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
                    <i class="fas fa-briefcase text-green-600"></i> Bidang Usaha & NIB
                </h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Bidang Usaha *</label>
                        <select wire:model.live="bidangUsaha" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">-- Pilih Bidang Usaha --</option>
                            @foreach($bidangUsahaList as $bidang)
                            <option value="{{ $bidang }}">{{ $bidang }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($bidangUsaha === 'Lainnya')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Sebutkan Bidang Usaha *</label>
                        <input type="text" wire:model.live="bidangUsahaLainnya" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Tuliskan bidang usaha">
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5">Nomor NIB (Opsional)</label>
                        <input type="text" wire:model.live="noNib" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Boleh dikosongkan jika belum punya">
                        <p class="mt-1 text-xs text-gray-400">Nomor Induk Berusaha - jika sudah memiliki</p>
                    </div>

                    <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center gap-2 text-green-700">
                            <i class="fas fa-info-circle"></i>
                            <span class="font-medium">Skema Bantuan Tunai (Cash Transfer)</span>
                        </div>
                        <p class="text-sm text-green-600 mt-1">Tidak ada input RAB/Pagu karena bantuan sesuai regulasi yang berlaku.</p>
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
                    <i class="fas fa-file-upload text-green-600"></i> Upload Dokumen Legalitas (Wajib)
                </h3>

                <div class="space-y-3">
                    {{-- SKU File --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5"><i class="fas fa-clipboard mr-1"></i> Surat Keterangan Usaha (SKU) dari Desa/Kelurahan *</label>
                        @if($isEditMode && isset($existingFiles['SKU']) && !$skuFile)
                        <div class="flex items-center gap-2 p-2 bg-green-50 border border-green-200 rounded-lg mb-1">
                            <i class="fas fa-file-alt text-green-600"></i>
                            <a href="{{ $existingFiles['SKU']['url'] }}" target="_blank" class="text-sm text-blue-600 hover:underline truncate">{{ $existingFiles['SKU']['name'] }}</a>
                            <span class="text-xs text-green-600 ml-auto flex-shrink-0"><i class="fas fa-check"></i> Ada</span>
                        </div>
                        @endif
                        <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed {{ $skuFile ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50 hover:border-green-400 hover:bg-green-50' }} rounded-xl cursor-pointer transition">
                            @if($skuFile)
                                <div class="flex items-center gap-2 text-green-600">
                                    <i class="fas fa-check-circle text-2xl"></i>
                                    <span class="text-sm font-medium">{{ $skuFile->getClientOriginalName() }}</span>
                                </div>
                                <span class="text-xs text-gray-400 mt-1">Klik untuk ganti file</span>
                            @else
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                                <span class="text-sm text-gray-500 mt-1">Seret & lepas atau klik untuk upload</span>
                                <span class="text-xs text-gray-400">PDF, JPG, PNG, maks 5MB</span>
                            @endif
                            <input type="file" wire:model.live="skuFile" accept=".pdf,.jpg,.jpeg,.png" class="sr-only">
                        </label>
                        <p class="mt-1 text-xs text-gray-400">Memuat nama usaha dan alamat usaha</p>
                    </div>

                    {{-- Foto KTP --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5"><i class="fas fa-id-card mr-1"></i> Foto KTP *</label>
                        @if($isEditMode && isset($existingFiles['Foto KTP']) && !$fotoKtpFile)
                        <div class="flex items-center gap-2 p-2 bg-green-50 border border-green-200 rounded-lg mb-1">
                            <i class="fas fa-id-card text-green-600"></i>
                            <a href="{{ $existingFiles['Foto KTP']['url'] }}" target="_blank" class="text-sm text-blue-600 hover:underline truncate">{{ $existingFiles['Foto KTP']['name'] }}</a>
                            <span class="text-xs text-green-600 ml-auto flex-shrink-0"><i class="fas fa-check"></i> Ada</span>
                        </div>
                        @endif
                        <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed {{ $fotoKtpFile ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50 hover:border-green-400 hover:bg-green-50' }} rounded-xl cursor-pointer transition">
                            @if($fotoKtpFile)
                                <div class="flex items-center gap-2 text-green-600">
                                    <i class="fas fa-check-circle text-2xl"></i>
                                    <span class="text-sm font-medium">{{ $fotoKtpFile->getClientOriginalName() }}</span>
                                </div>
                                <span class="text-xs text-gray-400 mt-1">Klik untuk ganti file</span>
                            @else
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                                <span class="text-sm text-gray-500 mt-1">Seret & lepas atau klik untuk upload</span>
                                <span class="text-xs text-gray-400">JPG, PNG, maks 5MB</span>
                            @endif
                            <input type="file" wire:model.live="fotoKtpFile" accept="image/*" class="sr-only">
                        </label>
                    </div>

                    {{-- Foto Rekening --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-0.5"><i class="fas fa-university mr-1"></i> Foto Buku Tabungan / Rekening *</label>
                        @if($isEditMode && isset($existingFiles['Foto Rekening']) && !$fotoRekening)
                        <div class="flex items-center gap-2 p-2 bg-green-50 border border-green-200 rounded-lg mb-1">
                            <i class="fas fa-university text-green-600"></i>
                            <a href="{{ $existingFiles['Foto Rekening']['url'] }}" target="_blank" class="text-sm text-blue-600 hover:underline truncate">{{ $existingFiles['Foto Rekening']['name'] }}</a>
                            <span class="text-xs text-green-600 ml-auto flex-shrink-0"><i class="fas fa-check"></i> Ada</span>
                        </div>
                        @endif
                        <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed {{ $fotoRekening ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50 hover:border-green-400 hover:bg-green-50' }} rounded-xl cursor-pointer transition">
                            @if($fotoRekening)
                                <div class="flex items-center gap-2 text-green-600">
                                    <i class="fas fa-check-circle text-2xl"></i>
                                    <span class="text-sm font-medium">{{ $fotoRekening->getClientOriginalName() }}</span>
                                </div>
                                <span class="text-xs text-gray-400 mt-1">Klik untuk ganti file</span>
                            @else
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                                <span class="text-sm text-gray-500 mt-1">Seret & lepas atau klik untuk upload</span>
                                <span class="text-xs text-gray-400">JPG, PNG, maks 5MB</span>
                            @endif
                            <input type="file" wire:model.live="fotoRekening" accept="image/*" class="sr-only">
                        </label>
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
                <button type="button" wire:click="saveDraft" class="px-4 py-2 border border-green-300 text-green-600 rounded-lg hover:bg-green-50 transition text-sm font-medium">
                    <span wire:loading.class="hidden" wire:target="saveDraft"><i class="fas fa-save mr-1"></i> Simpan Sementara</span>
                    <span wire:loading class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Menyimpan...</span>
                </button>
                @if($step < 3)
                <button type="submit" class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm">
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
