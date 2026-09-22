<?php

namespace App\Http\Livewire\UtusanDewan;

use App\Enums\JenisBantuan;
use App\Enums\UsulanStatus;
use App\Models\DataRekeningUep;
use App\Models\Draft;
use App\Models\LogAuditTrail;
use App\Models\MasterDesilSen;
use App\Models\MasterWilayah;
use App\Models\UsulanPokir;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class PengajuanUep extends Component
{
    use WithFileUploads;

    public $step = 1;

    // Tahun Asal Proposal
    public $tahunAsalProposal = '';

    // Step 1: Identitas & Rekening
    public $nik = '';
    public $nama = '';
    public $noHp = '';
    public $fotoKtp = null;
    public $desil = '';
    public $namaUsaha = '';

    public $desilList = [
        1 => 'Desil 1 — Sangat Miskin',
        2 => 'Desil 2 — Miskin',
        3 => 'Desil 3 — Rentan Miskin',
        4 => 'Desil 4 — Hampir Miskin',
    ];

    // Rekening
    public $namaBank = '';
    public $namaBankLainnya = '';
    public $nomorRekening = '';
    public $namaPemilikRekening = '';
    public $rekeningValid = false;
    public $rekeningMessage = '';

    // Lokasi
    public $kabupaten = '';
    public $kecamatan = '';
    public $desaKelurahan = '';
    public $alamatDetail = '';
    public $latitude = '';
    public $longitude = '';
    public $linkGoogleMaps = '';
    public $fotoUsaha = null;

    // Step 2: Bidang & NIB
    public $bidangUsaha = '';
    public $bidangUsahaLainnya = '';
    public $noNib = '';

    // Step 3: Dokumen
    public $skuFile = null;
    public $fotoKtpFile = null;
    public $fotoRekening = null;

    public $bankList = [
        'BCA', 'Mandiri', 'BRI', 'BNI', 'BTN', 'CIMB Niaga',
        'Danamon', 'Permata', 'BSI', 'BTPN', 'OCBC NISP', 'Lainnya',
    ];
    public $bidangUsahaList = [
        'Pertanian', 'Peternakan', 'Perikanan', 'Perdagangan',
        'Jasa', 'Industri Rumahan', 'Kerajinan Tangan', 'Lainnya',
    ];

    public $kabupatenList = [];
    public $kecamatanList = [];

    // DTSEN desil cache
    public $nikDesilCache = [];

    public $errors_list = [];
    public $successMessage = '';

    // Draft
    public $showDraftBanner = false;
    public $draftId = null;
    public $draftStep = 1;
    public $draftUpdatedAt = null;
    public $draftFilePaths = [];

    // Edit mode
    public $editId = null;
    public $isEditMode = false;
    public $catatanVerifikator = '';
    public $existingFiles = [];

    public function mount(?int $editId = null): void
    {
        $this->kabupatenList = MasterWilayah::distinct()->pluck('kabupaten')->sort()->toArray();

        // Edit mode: load existing usulan
        if ($editId) {
            $this->loadFromUsulan($editId);
            return;
        }

        if ($this->kabupaten) {
            $this->kecamatanList = MasterWilayah::where('kabupaten', $this->kabupaten)
                ->pluck('kecamatan')->sort()->toArray();
        }

        // Check for existing draft
        $this->checkForDraft();
    }

    private function checkForDraft(): void
    {
        $draft = Draft::where('user_id', auth()->id())
            ->where('jenis_bantuan', 'UEP')
            ->first();

        if ($draft) {
            $this->showDraftBanner = true;
            $this->draftId = $draft->id;
            $this->draftStep = $draft->current_step;
            $this->draftUpdatedAt = $draft->updated_at;
            $this->draftFilePaths = $draft->file_paths ?? [];
        }
    }

    private function loadFromUsulan(int $id): void
    {
        $usulan = UsulanPokir::with('rekening', 'dokumen')->findOrFail($id);

        $this->editId = $usulan->id;
        $this->isEditMode = true;
        $this->catatanVerifikator = $usulan->catatan_verifikator ?? '';

        // Step 1: Identitas
        $this->tahunAsalProposal = $usulan->tahun_asal_proposal;
        $this->nik = $usulan->nik;
        $this->nama = $usulan->nama_ketua_individu;
        $this->noHp = $usulan->no_hp;
        $this->desil = $usulan->desil;
        $this->namaUsaha = $usulan->nama_kelompok_usaha;

        // Rekening
        $rekening = $usulan->rekening;
        if ($rekening) {
            $this->namaBank = $rekening->nama_bank;
            $this->nomorRekening = $rekening->nomor_rekening;
            $this->namaPemilikRekening = $rekening->nama_pemilik_rekening;
        }

        // Lokasi
        $this->kabupaten = $usulan->kabupaten;
        $this->kecamatan = $usulan->kecamatan;
        $this->desaKelurahan = $usulan->desa_kelurahan;
        $this->alamatDetail = $usulan->alamat_detail;
        $this->latitude = $usulan->latitude;
        $this->longitude = $usulan->longitude;
        $this->linkGoogleMaps = $usulan->link_google_maps;

        $this->kecamatanList = MasterWilayah::where('kabupaten', $this->kabupaten)
            ->pluck('kecamatan')->sort()->toArray();

        // Step 2: Bidang & NIB
        $this->bidangUsaha = $usulan->bidang_usaha;
        $this->noNib = $usulan->no_nib ?? '';

        // Step 3: Existing files
        foreach ($usulan->dokumen as $doc) {
            $this->existingFiles[$doc->jenis_dokumen] = [
                'path' => $doc->file_path,
                'name' => $doc->nama_file,
                'url' => asset('storage/' . $doc->file_path),
            ];
        }
    }

    public function updatedKabupaten(): void
    {
        $this->kecamatan = '';
        $this->kecamatanList = MasterWilayah::where('kabupaten', $this->kabupaten)
            ->pluck('kecamatan')->sort()->toArray();
    }

    // ── DTSEN Desil Auto-Fill ───────────────────────────────────────
    public function updatedNik(): void
    {
        if (strlen($this->nik) !== 16 || !ctype_digit($this->nik)) return;
        if (isset($this->nikDesilCache[$this->nik])) return;

        $dtksen = MasterDesilSen::where('nik', $this->nik)->latest('tahun_sync')->first();

        if ($dtksen) {
            $this->nikDesilCache[$this->nik] = [
                'found' => true,
                'desil' => $dtksen->desil,
                'nama' => $dtksen->nama,
            ];
            if (empty($this->desil)) {
                $this->desil = $dtksen->desil;
            }
        } else {
            $this->nikDesilCache[$this->nik] = [
                'found' => false,
                'desil' => null,
                'nama' => null,
            ];
        }
    }

    /**
     * Get desil validation status for a given NIK.
     */
    public function getDesilStatus(?string $nik, $selectedDesil): ?string
    {
        if (empty($nik) || strlen($nik) !== 16) return null;
        if (!isset($this->nikDesilCache[$nik])) return null;

        $cache = $this->nikDesilCache[$nik];
        if (!$cache['found']) return 'not_found';
        if (empty($selectedDesil)) return null;
        if ((int) $selectedDesil === $cache['desil']) return 'verified';

        return 'mismatch';
    }

    public function updatedNama(): void
    {
        $this->validateRekening();
    }

    public function updatedNamaPemilikRekening(): void
    {
        $this->validateRekening();
    }

    public function validateRekening(): void
    {
        if (!$this->namaPemilikRekening || !$this->nama) return;

        $service = new \App\Services\UsulanService();
        $result = $service->validateRekening($this->namaPemilikRekening, $this->nama);
        $this->rekeningValid = $result['is_valid'];
        $this->rekeningMessage = $result['message'];
    }

    public function nextStep(): void
    {
        $this->clearErrors();

        if ($this->step === 1) {
            $this->validateStep1();
        } elseif ($this->step === 2) {
            $this->validateStep2();
        }

        if (empty($this->errors_list) && $this->step < 3) {
            $this->step++;
        }
    }

    public function prevStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    private function clearErrors(): void
    {
        $this->errors_list = [];
    }

    private function validateStep1(): void
    {
        $this->clearErrors();
        if (empty($this->nama)) $this->errors_list[] = 'Nama wajib diisi';
        if (strlen($this->nik) !== 16) $this->errors_list[] = 'NIK harus 16 digit';
        if (!ctype_digit($this->nik)) $this->errors_list[] = 'NIK harus berupa angka';
        if (empty($this->desil)) $this->errors_list[] = 'Desil wajib dipilih';
        if (empty($this->noHp)) $this->errors_list[] = 'No HP wajib diisi';
        if (empty($this->namaBank)) $this->errors_list[] = 'Nama Bank wajib dipilih';
        if ($this->namaBank === 'Lainnya' && empty($this->namaBankLainnya)) $this->errors_list[] = 'Nama Bank Lainnya wajib diisi';
        if (empty($this->nomorRekening)) $this->errors_list[] = 'Nomor Rekening wajib diisi';
        if (empty($this->namaPemilikRekening)) $this->errors_list[] = 'Nama Pemilik Rekening wajib diisi';
        if (!$this->rekeningValid) $this->errors_list[] = 'Nama pemilik rekening tidak sesuai KTP';
        if (empty($this->kabupaten)) $this->errors_list[] = 'Kabupaten wajib diisi';
        if (empty($this->kecamatan)) $this->errors_list[] = 'Kecamatan wajib diisi';
        if (empty($this->desaKelurahan)) $this->errors_list[] = 'Desa/Kelurahan wajib diisi';
        if (empty($this->alamatDetail)) $this->errors_list[] = 'Alamat detail wajib diisi';
        if (empty($this->latitude) || empty($this->longitude)) $this->errors_list[] = 'Pin GPS wajib diisi';
        if (!$this->fotoUsaha) $this->errors_list[] = 'Foto usaha sedang berjalan wajib diupload';

        // ── DTSEN Desil Mismatch ──
        $this->validateDesilAgainstDtksen($this->nik, $this->desil);
    }

    private function validateDesilAgainstDtksen(string $nik, $desil): void
    {
        if (strlen($nik) !== 16 || !ctype_digit($nik)) return;
        if (empty($desil)) return;
        if (!isset($this->nikDesilCache[$nik])) return;

        $cache = $this->nikDesilCache[$nik];
        if (!$cache['found']) return;

        if ((int) $desil !== $cache['desil']) {
            $this->errors_list[] = "Desil tidak cocok dengan data DTSEN (seharusnya Desil {$cache['desil']} — {$cache['nama']})";
        }
    }

    private function validateStep2(): void
    {
        $this->clearErrors();
        if (empty($this->bidangUsaha)) $this->errors_list[] = 'Bidang Usaha wajib dipilih';
        if ($this->bidangUsaha === 'Lainnya' && empty($this->bidangUsahaLainnya)) $this->errors_list[] = 'Bidang Usaha Lainnya wajib diisi';
        if (!empty($this->noNib) && !ctype_digit($this->noNib)) $this->errors_list[] = 'No NIB harus berupa angka';
    }

    // ── Draft (Simpan Sementara) ─────────────────────────────────────
    public function saveDraft(): void
    {
        $formData = [
            'step' => $this->step,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'noHp' => $this->noHp,
            'desil' => $this->desil,
            'namaUsaha' => $this->namaUsaha,
            'namaBank' => $this->namaBank,
            'namaBankLainnya' => $this->namaBankLainnya,
            'nomorRekening' => $this->nomorRekening,
            'namaPemilikRekening' => $this->namaPemilikRekening,
            'kabupaten' => $this->kabupaten,
            'kecamatan' => $this->kecamatan,
            'desaKelurahan' => $this->desaKelurahan,
            'alamatDetail' => $this->alamatDetail,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'linkGoogleMaps' => $this->linkGoogleMaps,
            'bidangUsaha' => $this->bidangUsaha,
            'bidangUsahaLainnya' => $this->bidangUsahaLainnya,
            'noNib' => $this->noNib,
        ];

        $filePaths = $this->storeDraftFiles();

        $draft = Draft::updateOrCreate(
            ['user_id' => auth()->id(), 'jenis_bantuan' => 'UEP'],
            [
                'current_step' => $this->step,
                'form_data' => $formData,
                'file_paths' => $filePaths,
            ]
        );

        $this->draftId = $draft->id;
        $this->draftStep = $draft->current_step;
        $this->draftUpdatedAt = $draft->updated_at;
        $this->draftFilePaths = $filePaths;
        $this->showDraftBanner = true;

        session()->flash('success', 'Draft berhasil disimpan!');
    }

    private function storeDraftFiles(): array
    {
        $paths = [];
        $draftDir = 'drafts/temp';

        $fileMap = [
            'fotoUsaha' => $this->fotoUsaha,
            'skuFile' => $this->skuFile,
            'fotoKtpFile' => $this->fotoKtpFile,
            'fotoRekening' => $this->fotoRekening,
        ];

        foreach ($fileMap as $key => $file) {
            if ($file) {
                $filename = $key . '_' . auth()->id() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($draftDir, $filename);
                $paths[$key] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }

        return $paths;
    }

    public function restoreDraft(): void
    {
        $draft = Draft::where('id', $this->draftId)
            ->where('user_id', auth()->id())
            ->first();
        if (!$draft) return;

        $data = $draft->form_data;

        $this->step = $data['step'] ?? 1;
        $this->nik = $data['nik'] ?? '';
        $this->nama = $data['nama'] ?? '';
        $this->noHp = $data['noHp'] ?? '';
        $this->desil = $data['desil'] ?? '';
        $this->namaUsaha = $data['namaUsaha'] ?? '';
        $this->namaBank = $data['namaBank'] ?? '';
        $this->namaBankLainnya = $data['namaBankLainnya'] ?? '';
        $this->nomorRekening = $data['nomorRekening'] ?? '';
        $this->namaPemilikRekening = $data['namaPemilikRekening'] ?? '';
        $this->kabupaten = $data['kabupaten'] ?? '';
        $this->kecamatan = $data['kecamatan'] ?? '';
        $this->desaKelurahan = $data['desaKelurahan'] ?? '';
        $this->alamatDetail = $data['alamatDetail'] ?? '';
        $this->latitude = $data['latitude'] ?? '';
        $this->longitude = $data['longitude'] ?? '';
        $this->linkGoogleMaps = $data['linkGoogleMaps'] ?? '';
        $this->bidangUsaha = $data['bidangUsaha'] ?? '';
        $this->bidangUsahaLainnya = $data['bidangUsahaLainnya'] ?? '';
        $this->noNib = $data['noNib'] ?? '';

        if ($this->kabupaten) {
            $this->kecamatanList = MasterWilayah::where('kabupaten', $this->kabupaten)
                ->pluck('kecamatan')->sort()->toArray();
        }

        $this->showDraftBanner = false;
    }

    public function deleteDraft(): void
    {
        if (!$this->draftId) return;

        Draft::where('id', $this->draftId)->delete();
        $this->showDraftBanner = false;
        $this->draftId = null;
        $this->draftFilePaths = [];
    }

    private function resolveFile($livewireProp, string $draftKey)
    {
        if ($livewireProp) return $livewireProp;

        if (!empty($this->draftFilePaths[$draftKey])) {
            $info = $this->draftFilePaths[$draftKey];
            $fullPath = storage_path('app/' . $info['path']);
            if (file_exists($fullPath)) {
                // Verify MIME from actual file content (defense-in-depth)
                $actualMime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fullPath);
                $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
                if (!in_array($actualMime, $allowedMimes)) {
                    return null;
                }

                return new UploadedFile(
                    $fullPath,
                    $info['original_name'],
                    $actualMime,
                    null,
                    true
                );
            }
        }

        return null;
    }

    private function cleanupDraftFiles(): void
    {
        if (!empty($this->draftFilePaths)) {
            foreach ($this->draftFilePaths as $info) {
                $path = storage_path('app/' . $info['path']);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
    }

    public function submit(): void
    {
        $this->clearErrors();

        // Re-validate all steps to prevent bypass via direct Livewire call
        $this->validateStep1();
        $this->validateStep2();

        // Resolve files — prefer Livewire upload, fallback to draft files
        $skuFile = $this->resolveFile($this->skuFile, 'skuFile');
        $fotoKtpFile = $this->resolveFile($this->fotoKtpFile, 'fotoKtpFile');
        $fotoRekening = $this->resolveFile($this->fotoRekening, 'fotoRekening');
        $fotoUsaha = $this->resolveFile($this->fotoUsaha, 'fotoUsaha');

        // Validate step 3 — in edit mode, allow existing files
        $hasExisting = fn(string $jenis) => $this->isEditMode && isset($this->existingFiles[$jenis]);
        if (!$skuFile && !$hasExisting('SKU')) $this->errors_list[] = 'SKU wajib diupload';
        if (!$fotoKtpFile && !$hasExisting('Foto KTP')) $this->errors_list[] = 'Foto KTP wajib diupload';
        if (!$fotoRekening && !$hasExisting('Foto Rekening')) $this->errors_list[] = 'Foto Buku Tabungan wajib diupload';

        // File size validation (max 5MB)
        $service = new \App\Services\UsulanService();
        if ($fotoUsaha && !$service->validateFileSize($fotoUsaha)) $this->errors_list[] = 'Foto Usaha maksimal 5MB';
        if ($skuFile && !$service->validateFileSize($skuFile)) $this->errors_list[] = 'SKU maksimal 5MB';
        if ($fotoKtpFile && !$service->validateFileSize($fotoKtpFile)) $this->errors_list[] = 'Foto KTP maksimal 5MB';
        if ($fotoRekening && !$service->validateFileSize($fotoRekening)) $this->errors_list[] = 'Foto Buku Tabungan maksimal 5MB';

        // Phone format validation (08xxxxxxxxxx)
        if (!empty($this->noHp) && !$service->validatePhone($this->noHp)) {
            $this->errors_list[] = 'No HP format tidak valid (contoh: 081234567890)';
        }

        if (!empty($this->errors_list)) return;

        DB::beginTransaction();

        try {
            // Determine desil validity from DTSEN cache
            $isDesilValid = $this->isDesilVerified($this->nik, $this->desil);

            $data = [
                'user_id' => auth()->id(),
                'anggota_dewan_id' => auth()->user()->anggota_dewan_id,
                'tahun_anggaran' => config('app.tahun_anggaran'),
                'tahun_asal_proposal' => (int) $this->tahunAsalProposal,
                'jenis_bantuan' => JenisBantuan::UEP,
                'nama_kelompok_usaha' => $this->namaUsaha,
                'nama_ketua_individu' => $this->nama,
                'nik' => $this->nik,
                'no_hp' => $this->noHp,
                'desil' => (int) $this->desil,
                'is_desil_valid' => $isDesilValid,
                'alamat_lengkap' => trim($this->kabupaten . ', ' . $this->kecamatan . ', ' . $this->desaKelurahan . '. ' . $this->alamatDetail),
                'kabupaten' => $this->kabupaten,
                'kecamatan' => $this->kecamatan,
                'desa_kelurahan' => $this->desaKelurahan,
                'alamat_detail' => $this->alamatDetail,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'link_google_maps' => $this->linkGoogleMaps,
                'bidang_usaha' => $this->bidangUsaha === 'Lainnya' ? $this->bidangUsahaLainnya : $this->bidangUsaha,
                'no_nib' => $this->noNib ?: null,
            ];

            $service = new \App\Services\UsulanService();

            if ($this->isEditMode) {
                // UPDATE existing usulan
                $usulan = UsulanPokir::findOrFail($this->editId);
                $usulan->update($data);

                // Update rekening
                $rekening = DataRekeningUep::where('usulan_id', $usulan->id)->first();
                if ($rekening) {
                    $rekening->update([
                        'nama_bank' => $this->namaBank === 'Lainnya' ? $this->namaBankLainnya : $this->namaBank,
                        'nomor_rekening' => $this->nomorRekening,
                        'nama_pemilik_rekening' => $this->namaPemilikRekening,
                    ]);
                } else {
                    DataRekeningUep::create([
                        'usulan_id' => $usulan->id,
                        'nama_bank' => $this->namaBank === 'Lainnya' ? $this->namaBankLainnya : $this->namaBank,
                        'nomor_rekening' => $this->nomorRekening,
                        'nama_pemilik_rekening' => $this->namaPemilikRekening,
                    ]);
                }

                // Replace documents (only if new files uploaded)
                $docTypes = [
                    'SKU' => $skuFile,
                    'Foto KTP' => $fotoKtpFile,
                    'Foto Rekening' => $fotoRekening,
                    'Foto Usaha' => $fotoUsaha,
                ];
                foreach ($docTypes as $jenis => $file) {
                    if ($file) {
                        $service->deleteDokumenByJenis($usulan->id, $jenis);
                        $this->uploadDokumen($usulan->id, $jenis, $file);
                    }
                }

                $service->submitUsulan($usulan);

                LogAuditTrail::log(
                    'EDIT_USULAN_UEP',
                    UsulanPokir::class,
                    $usulan->id,
                    null,
                    ['nama' => $this->nama, 'nik' => $this->nik, 'catatan_cleared' => true]
                );

                DB::commit();

                $this->reset();
                $this->successMessage = 'Usulan UEP #' . $usulan->id . ' berhasil diubah dan diajukan kembali!';

            } else {
                // CREATE new usulan
                $data['status'] = UsulanStatus::DRAFT;
                $usulan = UsulanPokir::create($data);

                // Save rekening
                DataRekeningUep::create([
                    'usulan_id' => $usulan->id,
                    'nama_bank' => $this->namaBank === 'Lainnya' ? $this->namaBankLainnya : $this->namaBank,
                    'nomor_rekening' => $this->nomorRekening,
                    'nama_pemilik_rekening' => $this->namaPemilikRekening,
                ]);

                // Upload files — use resolved files
                $this->uploadDokumen($usulan->id, 'SKU', $skuFile);
                $this->uploadDokumen($usulan->id, 'Foto KTP', $fotoKtpFile);
                $this->uploadDokumen($usulan->id, 'Foto Rekening', $fotoRekening);
                if ($fotoUsaha) {
                    $this->uploadDokumen($usulan->id, 'Foto Usaha', $fotoUsaha);
                }

                LogAuditTrail::log(
                    'CREATE_USULAN_UEP',
                    UsulanPokir::class,
                    $usulan->id,
                    null,
                    ['nama' => $this->nama, 'nik' => $this->nik]
                );

                // Delete draft after successful submit
                $this->deleteDraft();
                // Clean up draft files from storage
                $this->cleanupDraftFiles();

                DB::commit();

                $this->reset();
                $this->successMessage = 'Usulan UEP berhasil dibuat! Nomor: #' . $usulan->id;
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal menyimpan usulan UEP: ' . $e->getMessage(), ['exception' => $e]);
            $this->errors_list[] = 'Gagal menyimpan data. Silakan coba lagi atau hubungi admin.';
        }
    }

    private function uploadDokumen(int $usulanId, string $jenisDokumen, $file): void
    {
        $service = new \App\Services\UsulanService();
        $service->uploadDokumen($usulanId, $jenisDokumen, $file);
    }

    private function isDesilVerified(string $nik, $desil): bool
    {
        if (strlen($nik) !== 16 || !ctype_digit($nik)) return false;
        if (empty($desil)) return false;
        if (!isset($this->nikDesilCache[$nik])) return false;

        $cache = $this->nikDesilCache[$nik];
        return $cache['found'] && (int) $desil === $cache['desil'];
    }

    public function render()
    {
        return view('livewire.utusan-dewan.pengajuan-uep');
    }
}
