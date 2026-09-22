<?php

namespace App\Http\Livewire\UtusanDewan;

use App\Enums\JenisBantuan;
use App\Enums\UsulanStatus;
use App\Models\AnggotaKube;
use App\Models\Draft;
use App\Models\LogAuditTrail;
use App\Models\MasterDesilSen;
use App\Models\MasterStandarPagu;
use App\Models\MasterWilayah;
use App\Models\UsulanPokir;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class PengajuanKube extends Component
{
    use WithFileUploads;

    // Wizard step
    public $step = 1;

    // Tahun Asal Proposal
    public $tahunAsalProposal = '';

    // Step 1: Identitas Kelompok
    public $namaKube = '';
    public $sektorUsaha = '';
    public $sektorUsahaLainnya = '';
    public $jumlahAnggota = '';

    // Data Ketua
    public $nikKetua = '';
    public $noKkKetua = '';
    public $namaKetua = '';
    public $noHpKetua = '';
    public $desilKetua = '';
    public $fotoKtpKetua = null;

    // Data Sekretaris
    public $nikSekretaris = '';
    public $noKkSekretaris = '';
    public $namaSekretaris = '';
    public $noHpSekretaris = '';
    public $desilSekretaris = '';
    public $fotoKtpSekretaris = null;

    // Data Bendahara
    public $nikBendahara = '';
    public $noKkBendahara = '';
    public $namaBendahara = '';
    public $noHpBendahara = '';
    public $desilBendahara = '';
    public $fotoKtpBendahara = null;

    // Anggota Tambahan (dynamic array)
    public $anggota = [];

    // Lokasi
    public $kabupaten = '';
    public $kecamatan = '';
    public $desaKelurahan = '';
    public $alamatDetail = '';
    public $latitude = '';
    public $longitude = '';
    public $linkGoogleMaps = '';
    public $fotoLokasi = null;

    // Step 2: Bidang & RAB
    public $bidangUsaha = '';
    public $bidangUsahaLainnya = '';
    public $paguMaksimal = 0;
    public $totalRab = '';
    public $paguMessage = '';

    // Step 3: Dokumen
    public $proposalPdf = null;
    public $legalitasPdf = null;
    public $bukuRekening = null;
    public $dokumentasiUsaha = [];

    // Lists
    public $sektorUsahaList = [
        'Pertanian', 'Peternakan', 'Perikanan', 'Perdagangan',
        'Jasa', 'Industri Rumahan', 'Kerajinan Tangan', 'Lainnya',
    ];
    public $bidangUsahaList = [];
    public $kabupatenList = [];
    public $kecamatanList = [];
    public $desilList = [
        1 => 'Desil 1 — Sangat Miskin',
        2 => 'Desil 2 — Miskin',
        3 => 'Desil 3 — Rentan Miskin',
        4 => 'Desil 4 — Hampir Miskin',
    ];

    // DTSEN desil cache — [nik => ['found' => bool, 'desil' => int|null, 'nama' => string|null]]
    public $nikDesilCache = [];

    // Error
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
        $this->bidangUsahaList = MasterStandarPagu::where('tahun_anggaran', config('app.tahun_anggaran'))
            ->pluck('bidang_usaha')
            ->toArray();

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
            ->where('jenis_bantuan', 'KUBE')
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
        $usulan = UsulanPokir::with('anggota', 'dokumen')->findOrFail($id);

        $this->editId = $usulan->id;
        $this->isEditMode = true;
        $this->catatanVerifikator = $usulan->catatan_verifikator ?? '';

        // Step 1: Identitas
        $this->tahunAsalProposal = $usulan->tahun_asal_proposal;
        $this->namaKube = $usulan->nama_kelompok_usaha;
        $this->sektorUsaha = $usulan->sektor_usaha;
        $this->sektorUsahaLainnya = $usulan->sektor_usaha_lainnya ?? '';
        $this->jumlahAnggota = $usulan->jumlah_anggota;

        // Ketua
        $this->nikKetua = $usulan->nik;
        $this->noKkKetua = $usulan->no_kk_ketua;
        $this->namaKetua = $usulan->nama_ketua_individu;
        $this->noHpKetua = $usulan->no_hp;
        $this->desilKetua = $usulan->desil;

        // Sekretaris
        $this->nikSekretaris = $usulan->nik_sekretaris;
        $this->noKkSekretaris = $usulan->no_kk_sekretaris;
        $this->namaSekretaris = $usulan->nama_sekretaris;
        $this->noHpSekretaris = $usulan->no_hp_sekretaris;
        $this->desilSekretaris = $usulan->desil_sekretaris;

        // Bendahara
        $this->nikBendahara = $usulan->nik_bendahara;
        $this->noKkBendahara = $usulan->no_kk_bendahara;
        $this->namaBendahara = $usulan->nama_bendahara;
        $this->noHpBendahara = $usulan->no_hp_bendahara;
        $this->desilBendahara = $usulan->desil_bendahara;

        // Anggota
        $this->anggota = $usulan->anggota->map(fn($a) => [
            'nik' => $a->nik,
            'no_kk' => $a->no_kk,
            'nama' => $a->nama,
            'no_hp' => $a->no_hp,
            'desil' => $a->desil,
        ])->toArray();

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

        // Step 2: Bidang & RAB
        $this->bidangUsaha = $usulan->bidang_usaha;
        $this->totalRab = $usulan->total_rab;

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
    public function updatedNikKetua(): void
    {
        $this->lookupDtksenAndFillDesil($this->nikKetua, 'desilKetua');
    }

    public function updatedNikSekretaris(): void
    {
        $this->lookupDtksenAndFillDesil($this->nikSekretaris, 'desilSekretaris');
    }

    public function updatedNikBendahara(): void
    {
        $this->lookupDtksenAndFillDesil($this->nikBendahara, 'desilBendahara');
    }

    public function updatedAnggotaNik($index): void
    {
        $nik = $this->anggota[$index]['nik'] ?? '';
        if (strlen($nik) === 16 && ctype_digit($nik)) {
            $this->lookupDtksenAndFillDesil($nik, "anggota.{$index}.desil");
        }
    }

    private function lookupDtksenAndFillDesil(string $nik, string $desilField): void
    {
        if (strlen($nik) !== 16 || !ctype_digit($nik)) return;

        // Cache hit — skip query
        if (isset($this->nikDesilCache[$nik])) return;

        $dtksen = MasterDesilSen::where('nik', $nik)->latest('tahun_sync')->first();

        if ($dtksen) {
            $this->nikDesilCache[$nik] = [
                'found' => true,
                'desil' => $dtksen->desil,
                'nama' => $dtksen->nama,
            ];
            // Auto-fill desil only if field is empty
            $currentDesil = $this->getPropertyValue($desilField);
            if (empty($currentDesil)) {
                $this->setProperty($desilField, $dtksen->desil);
            }
        } else {
            $this->nikDesilCache[$nik] = [
                'found' => false,
                'desil' => null,
                'nama' => null,
            ];
        }
    }

    /**
     * Get desil validation status for a given NIK.
     * Returns: null (no lookup yet), 'verified', 'not_found', or 'mismatch'
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

    public function updatedBidangUsaha(): void
    {
        $this->validatePagu();
    }

    public function updatedTotalRab(): void
    {
        $this->validatePagu();
    }

    public function validatePagu(): void
    {
        if (!$this->bidangUsaha || !$this->totalRab) return;

        $bidangUsaha = $this->bidangUsaha === 'Lainnya' ? $this->bidangUsahaLainnya : $this->bidangUsaha;
        $service = new \App\Services\UsulanService();
        $result = $service->validatePagu($bidangUsaha, (float) $this->totalRab, config('app.tahun_anggaran'));
        $this->paguMaksimal = $result['pagu_maksimal'] ?? 0;
        $this->paguMessage = $result['message'];
    }

    // Anggota tambahan management
    public function addAnggota(): void
    {
        $this->anggota[] = [
            'nik' => '', 'no_kk' => '', 'nama' => '', 'no_hp' => '', 'desil' => '',
        ];
    }

    public function removeAnggota(int $index): void
    {
        unset($this->anggota[$index]);
        $this->anggota = array_values($this->anggota);
    }

    public function removeFoto(int $index): void
    {
        array_splice($this->dokumentasiUsaha, $index, 1);
    }

    // ── Draft (Simpan Sementara) ─────────────────────────────────────
    public function saveDraft(): void
    {
        $formData = [
            'step' => $this->step,
            'namaKube' => $this->namaKube,
            'sektorUsaha' => $this->sektorUsaha,
            'sektorUsahaLainnya' => $this->sektorUsahaLainnya,
            'jumlahAnggota' => $this->jumlahAnggota,
            'nikKetua' => $this->nikKetua,
            'noKkKetua' => $this->noKkKetua,
            'namaKetua' => $this->namaKetua,
            'noHpKetua' => $this->noHpKetua,
            'desilKetua' => $this->desilKetua,
            'nikSekretaris' => $this->nikSekretaris,
            'noKkSekretaris' => $this->noKkSekretaris,
            'namaSekretaris' => $this->namaSekretaris,
            'noHpSekretaris' => $this->noHpSekretaris,
            'desilSekretaris' => $this->desilSekretaris,
            'nikBendahara' => $this->nikBendahara,
            'noKkBendahara' => $this->noKkBendahara,
            'namaBendahara' => $this->namaBendahara,
            'noHpBendahara' => $this->noHpBendahara,
            'desilBendahara' => $this->desilBendahara,
            'anggota' => $this->anggota,
            'kabupaten' => $this->kabupaten,
            'kecamatan' => $this->kecamatan,
            'desaKelurahan' => $this->desaKelurahan,
            'alamatDetail' => $this->alamatDetail,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'linkGoogleMaps' => $this->linkGoogleMaps,
            'bidangUsaha' => $this->bidangUsaha,
            'bidangUsahaLainnya' => $this->bidangUsahaLainnya,
            'totalRab' => $this->totalRab,
        ];

        $filePaths = $this->storeDraftFiles();

        $draft = Draft::updateOrCreate(
            ['user_id' => auth()->id(), 'jenis_bantuan' => 'KUBE'],
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
            'fotoKtpKetua' => $this->fotoKtpKetua,
            'fotoKtpSekretaris' => $this->fotoKtpSekretaris,
            'fotoKtpBendahara' => $this->fotoKtpBendahara,
            'fotoLokasi' => $this->fotoLokasi,
            'proposalPdf' => $this->proposalPdf,
            'legalitasPdf' => $this->legalitasPdf,
            'bukuRekening' => $this->bukuRekening,
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

        foreach ($this->dokumentasiUsaha as $i => $foto) {
            $key = 'dokumentasiUsaha_' . $i;
            $filename = $key . '_' . auth()->id() . '_' . time() . '.' . $foto->getClientOriginalExtension();
            $path = $foto->storeAs($draftDir, $filename, 'public');
            $paths[$key] = [
                'path' => $path,
                'original_name' => $foto->getClientOriginalName(),
                'mime_type' => $foto->getMimeType(),
            ];
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
        $this->namaKube = $data['namaKube'] ?? '';
        $this->sektorUsaha = $data['sektorUsaha'] ?? '';
        $this->sektorUsahaLainnya = $data['sektorUsahaLainnya'] ?? '';
        $this->jumlahAnggota = $data['jumlahAnggota'] ?? '';
        $this->nikKetua = $data['nikKetua'] ?? '';
        $this->noKkKetua = $data['noKkKetua'] ?? '';
        $this->namaKetua = $data['namaKetua'] ?? '';
        $this->noHpKetua = $data['noHpKetua'] ?? '';
        $this->desilKetua = $data['desilKetua'] ?? '';
        $this->nikSekretaris = $data['nikSekretaris'] ?? '';
        $this->noKkSekretaris = $data['noKkSekretaris'] ?? '';
        $this->namaSekretaris = $data['namaSekretaris'] ?? '';
        $this->noHpSekretaris = $data['noHpSekretaris'] ?? '';
        $this->desilSekretaris = $data['desilSekretaris'] ?? '';
        $this->nikBendahara = $data['nikBendahara'] ?? '';
        $this->noKkBendahara = $data['noKkBendahara'] ?? '';
        $this->namaBendahara = $data['namaBendahara'] ?? '';
        $this->noHpBendahara = $data['noHpBendahara'] ?? '';
        $this->desilBendahara = $data['desilBendahara'] ?? '';
        $this->anggota = $data['anggota'] ?? [];
        $this->kabupaten = $data['kabupaten'] ?? '';
        $this->kecamatan = $data['kecamatan'] ?? '';
        $this->desaKelurahan = $data['desaKelurahan'] ?? '';
        $this->alamatDetail = $data['alamatDetail'] ?? '';
        $this->latitude = $data['latitude'] ?? '';
        $this->longitude = $data['longitude'] ?? '';
        $this->linkGoogleMaps = $data['linkGoogleMaps'] ?? '';
        $this->bidangUsaha = $data['bidangUsaha'] ?? '';
        $this->bidangUsahaLainnya = $data['bidangUsahaLainnya'] ?? '';
        $this->totalRab = $data['totalRab'] ?? '';

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
        // Kelompok
        if (empty($this->namaKube)) $this->errors_list[] = 'Nama KUBE wajib diisi';
        if (empty($this->sektorUsaha)) $this->errors_list[] = 'Sektor Usaha wajib dipilih';
        if ($this->sektorUsaha === 'Lainnya' && empty($this->sektorUsahaLainnya)) $this->errors_list[] = 'Sektor Usaha Lainnya wajib diisi';
        if (empty($this->jumlahAnggota) || $this->jumlahAnggota < 5) $this->errors_list[] = 'Jumlah anggota minimal 5';

        // Ketua
        if (empty($this->namaKetua)) $this->errors_list[] = 'Nama Ketua wajib diisi';
        if (strlen($this->nikKetua) !== 16) $this->errors_list[] = 'NIK Ketua harus 16 digit';
        if (!ctype_digit($this->nikKetua)) $this->errors_list[] = 'NIK Ketua harus berupa angka';
        if (strlen($this->noKkKetua) !== 16) $this->errors_list[] = 'No KK Ketua harus 16 digit';
        if (!ctype_digit($this->noKkKetua)) $this->errors_list[] = 'No KK Ketua harus berupa angka';
        if (empty($this->noHpKetua)) $this->errors_list[] = 'No HP Ketua wajib diisi';
        if (empty($this->desilKetua)) $this->errors_list[] = 'Desil Ketua wajib dipilih';
        if (!$this->fotoKtpKetua) $this->errors_list[] = 'Foto KTP Ketua wajib diupload';

        // Sekretaris
        if (empty($this->namaSekretaris)) $this->errors_list[] = 'Nama Sekretaris wajib diisi';
        if (strlen($this->nikSekretaris) !== 16) $this->errors_list[] = 'NIK Sekretaris harus 16 digit';
        if (!ctype_digit($this->nikSekretaris)) $this->errors_list[] = 'NIK Sekretaris harus berupa angka';
        if (strlen($this->noKkSekretaris) !== 16) $this->errors_list[] = 'No KK Sekretaris harus 16 digit';
        if (!ctype_digit($this->noKkSekretaris)) $this->errors_list[] = 'No KK Sekretaris harus berupa angka';
        if (empty($this->noHpSekretaris)) $this->errors_list[] = 'No HP Sekretaris wajib diisi';
        if (empty($this->desilSekretaris)) $this->errors_list[] = 'Desil Sekretaris wajib dipilih';
        if (!$this->fotoKtpSekretaris) $this->errors_list[] = 'Foto KTP Sekretaris wajib diupload';

        // Bendahara
        if (empty($this->namaBendahara)) $this->errors_list[] = 'Nama Bendahara wajib diisi';
        if (strlen($this->nikBendahara) !== 16) $this->errors_list[] = 'NIK Bendahara harus 16 digit';
        if (!ctype_digit($this->nikBendahara)) $this->errors_list[] = 'NIK Bendahara harus berupa angka';
        if (strlen($this->noKkBendahara) !== 16) $this->errors_list[] = 'No KK Bendahara harus 16 digit';
        if (!ctype_digit($this->noKkBendahara)) $this->errors_list[] = 'No KK Bendahara harus berupa angka';
        if (empty($this->noHpBendahara)) $this->errors_list[] = 'No HP Bendahara wajib diisi';
        if (empty($this->desilBendahara)) $this->errors_list[] = 'Desil Bendahara wajib dipilih';
        if (!$this->fotoKtpBendahara) $this->errors_list[] = 'Foto KTP Bendahara wajib diupload';

        // Anggota tambahan — validate each
        foreach ($this->anggota as $i => $ang) {
            $num = $i + 1;
            if (empty($ang['nama'])) $this->errors_list[] = "Nama Anggota {$num} wajib diisi";
            if (strlen($ang['nik'] ?? '') !== 16) $this->errors_list[] = "NIK Anggota {$num} harus 16 digit";
            if (!ctype_digit($ang['nik'] ?? '')) $this->errors_list[] = "NIK Anggota {$num} harus berupa angka";
            if (strlen($ang['no_kk'] ?? '') !== 16) $this->errors_list[] = "No KK Anggota {$num} harus 16 digit";
            if (!ctype_digit($ang['no_kk'] ?? '')) $this->errors_list[] = "No KK Anggota {$num} harus berupa angka";
            if (empty($ang['no_hp'])) $this->errors_list[] = "No HP Anggota {$num} wajib diisi";
            if (empty($ang['desil'])) $this->errors_list[] = "Desil Anggota {$num} wajib dipilih";
        }

        // Anggota count validation — user-specified jumlahAnggota vs actual array
        if (count($this->anggota) < 1) {
            $this->errors_list[] = 'Minimal harus menambahkan 1 anggota tambahan';
        }

        // KK uniqueness
        $allKk = array_filter([
            $this->noKkKetua,
            $this->noKkSekretaris,
            $this->noKkBendahara,
        ]);
        foreach ($this->anggota as $ang) {
            if (!empty($ang['no_kk'])) $allKk[] = $ang['no_kk'];
        }
        if (count($allKk) !== count(array_unique($allKk))) {
            $this->errors_list[] = 'Nomor KK tidak boleh ada yang sama dalam 1 KUBE';
        }

        // NIK uniqueness — same person cannot hold multiple positions
        $allNik = array_filter([
            $this->nikKetua,
            $this->nikSekretaris,
            $this->nikBendahara,
        ]);
        foreach ($this->anggota as $ang) {
            if (!empty($ang['nik'])) $allNik[] = $ang['nik'];
        }
        if (count($allNik) !== count(array_unique($allNik))) {
            $this->errors_list[] = 'NIK tidak boleh ada yang sama dalam 1 KUBE (satu orang tidak boleh menempati lebih dari 1 posisi)';
        }

        // Lokasi
        if (empty($this->kabupaten)) $this->errors_list[] = 'Kabupaten wajib diisi';
        if (empty($this->kecamatan)) $this->errors_list[] = 'Kecamatan wajib diisi';
        if (empty($this->desaKelurahan)) $this->errors_list[] = 'Desa/Kelurahan wajib diisi';
        if (empty($this->alamatDetail)) $this->errors_list[] = 'Alamat detail wajib diisi';
        if (empty($this->latitude) || empty($this->longitude)) $this->errors_list[] = 'Pin GPS wajib diisi';
        if (!$this->fotoLokasi) $this->errors_list[] = 'Foto tampak depan lokasi wajib diupload';

        // ── DTSEN Desil Mismatch (block submit if NIK found in DTSEN but desil doesn't match) ──
        $this->validateDesilAgainstDtksen('Ketua', $this->nikKetua, $this->desilKetua);
        $this->validateDesilAgainstDtksen('Sekretaris', $this->nikSekretaris, $this->desilSekretaris);
        $this->validateDesilAgainstDtksen('Bendahara', $this->nikBendahara, $this->desilBendahara);
        foreach ($this->anggota as $i => $ang) {
            $this->validateDesilAgainstDtksen('Anggota ' . ($i + 1), $ang['nik'] ?? '', $ang['desil'] ?? '');
        }
    }

    private function validateDesilAgainstDtksen(string $label, string $nik, $desil): void
    {
        if (strlen($nik) !== 16 || !ctype_digit($nik)) return;
        if (empty($desil)) return;
        if (!isset($this->nikDesilCache[$nik])) return;

        $cache = $this->nikDesilCache[$nik];
        if (!$cache['found']) return;

        if ((int) $desil !== $cache['desil']) {
            $this->errors_list[] = "Desil {$label} tidak cocok dengan data DTSEN (seharusnya Desil {$cache['desil']} — {$cache['nama']})";
        }
    }

    private function validateStep2(): void
    {
        $this->clearErrors();
        if (empty($this->bidangUsaha)) $this->errors_list[] = 'Bidang Usaha wajib dipilih';
        if ($this->bidangUsaha === 'Lainnya' && empty($this->bidangUsahaLainnya)) $this->errors_list[] = 'Bidang Usaha Lainnya wajib diisi';
        if (empty($this->totalRab) || $this->totalRab <= 0) $this->errors_list[] = 'Total RAB wajib diisi';

        $bidangUsaha = $this->bidangUsaha === 'Lainnya' ? $this->bidangUsahaLainnya : $this->bidangUsaha;

        if ($bidangUsaha && $this->totalRab) {
            $service = new \App\Services\UsulanService();
            $result = $service->validatePagu($bidangUsaha, (float) $this->totalRab, config('app.tahun_anggaran'));
            if (!$result['is_valid']) {
                $this->errors_list[] = $result['message'];
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
        $proposalPdf = $this->resolveFile($this->proposalPdf, 'proposalPdf');
        $legalitasPdf = $this->resolveFile($this->legalitasPdf, 'legalitasPdf');
        $bukuRekening = $this->resolveFile($this->bukuRekening, 'bukuRekening');
        $fotoKtpKetua = $this->resolveFile($this->fotoKtpKetua, 'fotoKtpKetua');
        $fotoKtpSekretaris = $this->resolveFile($this->fotoKtpSekretaris, 'fotoKtpSekretaris');
        $fotoKtpBendahara = $this->resolveFile($this->fotoKtpBendahara, 'fotoKtpBendahara');
        $fotoLokasi = $this->resolveFile($this->fotoLokasi, 'fotoLokasi');

        // Resolve dokumentasiUsaha — combine Livewire uploads + draft files
        $dokumentasi = $this->dokumentasiUsaha;
        if (empty($dokumentasi) && !empty($this->draftFilePaths)) {
            for ($i = 0; $i < 5; $i++) {
                $key = 'dokumentasiUsaha_' . $i;
                $file = $this->resolveFile(null, $key);
                if ($file) $dokumentasi[] = $file;
            }
        }

        // Validate step 3 — in edit mode, allow existing files
        $hasExisting = fn(string $jenis) => $this->isEditMode && isset($this->existingFiles[$jenis]);
        if (!$proposalPdf && !$hasExisting('Proposal')) $this->errors_list[] = 'Proposal PDF wajib diupload';
        if (!$legalitasPdf && !$hasExisting('Legalitas')) $this->errors_list[] = 'Surat Legalitas wajib diupload';
        if (!$bukuRekening && !$hasExisting('Buku Rekening')) $this->errors_list[] = 'Foto Buku Rekening wajib diupload';
        $fotoCount = count($dokumentasi) + ($this->isEditMode ? substr_count(implode(',', array_keys($this->existingFiles)), 'Foto Kegiatan Usaha') : 0);
        // Only enforce foto min/max when new fotos uploaded (edit mode with no new fotos = keep existing)
        if (!empty($dokumentasi)) {
            if (count($dokumentasi) < 3 && !$this->isEditMode) $this->errors_list[] = 'Dokumentasi Foto Kegiatan Usaha wajib minimal 3 foto';
            if (count($dokumentasi) > 5) $this->errors_list[] = 'Dokumentasi Foto Kegiatan Usaha maksimal 5 foto';
        }

        // File size validation (max 5MB)
        $service = new \App\Services\UsulanService();
        if ($proposalPdf && !$service->validateFileSize($proposalPdf)) $this->errors_list[] = 'Proposal PDF maksimal 5MB';
        if ($legalitasPdf && !$service->validateFileSize($legalitasPdf)) $this->errors_list[] = 'Surat Legalitas maksimal 5MB';
        if ($bukuRekening && !$service->validateFileSize($bukuRekening)) $this->errors_list[] = 'Buku Rekening maksimal 5MB';
        foreach ($dokumentasi as $i => $foto) {
            if (!$service->validateFileSize($foto)) $this->errors_list[] = 'Foto Kegiatan Usaha ' . ($i + 1) . ' maksimal 5MB';
        }

        // Phone format validation (08xxxxxxxxxx)
        $phones = array_filter([
            'Ketua' => $this->noHpKetua,
            'Sekretaris' => $this->noHpSekretaris,
            'Bendahara' => $this->noHpBendahara,
        ]);
        foreach ($phones as $role => $phone) {
            if (!empty($phone) && !$service->validatePhone($phone)) {
                $this->errors_list[] = "No HP $role format tidak valid (contoh: 081234567890)";
            }
        }
        foreach ($this->anggota as $i => $ang) {
            if (!empty($ang['no_hp']) && !$service->validatePhone($ang['no_hp'])) {
                $this->errors_list[] = "No HP Anggota " . ($i + 1) . " format tidak valid (contoh: 081234567890)";
            }
        }

        if (!empty($this->errors_list)) return;

        DB::beginTransaction();

        try {
            // Determine desil validity from DTSEN cache
            $isDesilValid = $this->isDesilVerified($this->nikKetua, $this->desilKetua);
            $isDesilSekValid = $this->isDesilVerified($this->nikSekretaris, $this->desilSekretaris);
            $isDesilBenValid = $this->isDesilVerified($this->nikBendahara, $this->desilBendahara);

            $data = [
                'user_id' => auth()->id(),
                'anggota_dewan_id' => auth()->user()->anggota_dewan_id,
                'tahun_anggaran' => config('app.tahun_anggaran'),
                'tahun_asal_proposal' => (int) $this->tahunAsalProposal,
                'jenis_bantuan' => JenisBantuan::KUBE,
                'nama_kelompok_usaha' => $this->namaKube,
                'sektor_usaha' => $this->sektorUsaha,
                'sektor_usaha_lainnya' => $this->sektorUsaha === 'Lainnya' ? $this->sektorUsahaLainnya : null,
                'jumlah_anggota' => (int) $this->jumlahAnggota,
                // Ketua
                'nama_ketua_individu' => $this->namaKetua,
                'nik' => $this->nikKetua,
                'no_kk_ketua' => $this->noKkKetua,
                'no_hp' => $this->noHpKetua,
                'desil' => (int) $this->desilKetua,
                'is_desil_valid' => $isDesilValid,
                // Sekretaris
                'nik_sekretaris' => $this->nikSekretaris,
                'no_kk_sekretaris' => $this->noKkSekretaris,
                'nama_sekretaris' => $this->namaSekretaris,
                'no_hp_sekretaris' => $this->noHpSekretaris,
                'desil_sekretaris' => (int) $this->desilSekretaris,
                'is_desil_sekretaris_valid' => $isDesilSekValid,
                // Bendahara
                'nik_bendahara' => $this->nikBendahara,
                'no_kk_bendahara' => $this->noKkBendahara,
                'nama_bendahara' => $this->namaBendahara,
                'no_hp_bendahara' => $this->noHpBendahara,
                'desil_bendahara' => (int) $this->desilBendahara,
                'is_desil_bendahara_valid' => $isDesilBenValid,
                // Lokasi
                'alamat_lengkap' => trim($this->kabupaten . ', ' . $this->kecamatan . ', ' . $this->desaKelurahan . '. ' . $this->alamatDetail),
                'kabupaten' => $this->kabupaten,
                'kecamatan' => $this->kecamatan,
                'desa_kelurahan' => $this->desaKelurahan,
                'alamat_detail' => $this->alamatDetail,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'link_google_maps' => $this->linkGoogleMaps,
                // Bidang & RAB
                'bidang_usaha' => $this->bidangUsaha === 'Lainnya' ? $this->bidangUsahaLainnya : $this->bidangUsaha,
                'total_rab' => $this->totalRab,
            ];

            $service = new \App\Services\UsulanService();

            if ($this->isEditMode) {
                // UPDATE existing usulan
                $usulan = UsulanPokir::findOrFail($this->editId);
                $usulan->update($data);

                // Replace anggota
                $service->deleteAnggotaByUsulan($usulan->id);
                foreach ($this->anggota as $ang) {
                    AnggotaKube::create([
                        'usulan_id' => $usulan->id,
                        'nik' => $ang['nik'],
                        'no_kk' => $ang['no_kk'],
                        'nama' => $ang['nama'],
                        'no_hp' => $ang['no_hp'],
                        'desil' => (int) $ang['desil'],
                        'is_desil_valid' => $this->isDesilVerified($ang['nik'] ?? '', $ang['desil'] ?? ''),
                    ]);
                }

                // Replace documents (only if new files uploaded)
                $docTypes = [
                    'Proposal' => $proposalPdf,
                    'Legalitas' => $legalitasPdf,
                    'Buku Rekening' => $bukuRekening,
                    'Foto KTP Ketua' => $fotoKtpKetua,
                    'Foto KTP Sekretaris' => $fotoKtpSekretaris,
                    'Foto KTP Bendahara' => $fotoKtpBendahara,
                    'Foto Lokasi' => $fotoLokasi,
                ];
                foreach ($docTypes as $jenis => $file) {
                    if ($file) {
                        $service->deleteDokumenByJenis($usulan->id, $jenis);
                        $this->uploadDokumen($usulan->id, $jenis, $file);
                    }
                }
                // Dokumentasi — always replace if new files provided
                if (!empty($dokumentasi)) {
                    for ($i = 1; $i <= 5; $i++) {
                        $service->deleteDokumenByJenis($usulan->id, 'Foto Kegiatan Usaha ' . $i);
                    }
                    foreach ($dokumentasi as $i => $foto) {
                        $this->uploadDokumen($usulan->id, 'Foto Kegiatan Usaha ' . ($i + 1), $foto);
                    }
                }

                $service->submitUsulan($usulan);

                LogAuditTrail::log(
                    'EDIT_USULAN_KUBE',
                    UsulanPokir::class,
                    $usulan->id,
                    null,
                    ['nama' => $this->namaKube, 'nik' => $this->nikKetua, 'catatan_cleared' => true]
                );

                DB::commit();

                $this->reset();
                $this->successMessage = 'Usulan KUBE #' . $usulan->id . ' berhasil diubah dan diajukan kembali!';

            } else {
                // CREATE new usulan
                $data['status'] = UsulanStatus::DRAFT;
                $usulan = UsulanPokir::create($data);

                // Simpan anggota tambahan
                foreach ($this->anggota as $ang) {
                    AnggotaKube::create([
                        'usulan_id' => $usulan->id,
                        'nik' => $ang['nik'],
                        'no_kk' => $ang['no_kk'],
                        'nama' => $ang['nama'],
                        'no_hp' => $ang['no_hp'],
                        'desil' => (int) $ang['desil'],
                        'is_desil_valid' => $this->isDesilVerified($ang['nik'] ?? '', $ang['desil'] ?? ''),
                    ]);
                }

                // Upload dokumen — use resolved files
                $this->uploadDokumen($usulan->id, 'Proposal', $proposalPdf);
                $this->uploadDokumen($usulan->id, 'Legalitas', $legalitasPdf);
                $this->uploadDokumen($usulan->id, 'Buku Rekening', $bukuRekening);
                foreach ($dokumentasi as $i => $foto) {
                    $this->uploadDokumen($usulan->id, 'Foto Kegiatan Usaha ' . ($i + 1), $foto);
                }
                if ($fotoKtpKetua) {
                    $this->uploadDokumen($usulan->id, 'Foto KTP Ketua', $fotoKtpKetua);
                }
                if ($fotoKtpSekretaris) {
                    $this->uploadDokumen($usulan->id, 'Foto KTP Sekretaris', $fotoKtpSekretaris);
                }
                if ($fotoKtpBendahara) {
                    $this->uploadDokumen($usulan->id, 'Foto KTP Bendahara', $fotoKtpBendahara);
                }
                if ($fotoLokasi) {
                    $this->uploadDokumen($usulan->id, 'Foto Lokasi', $fotoLokasi);
                }

                LogAuditTrail::log(
                    'CREATE_USULAN_KUBE',
                    UsulanPokir::class,
                    $usulan->id,
                    null,
                    ['nama' => $this->namaKube, 'nik' => $this->nikKetua]
                );

                // Delete draft after successful submit
                $this->deleteDraft();
                // Clean up draft files from storage
                $this->cleanupDraftFiles();

                DB::commit();

                $this->reset();
                $this->successMessage = 'Usulan KUBE berhasil dibuat! Nomor: #' . $usulan->id;
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal menyimpan usulan KUBE: ' . $e->getMessage(), ['exception' => $e]);
            $this->errors_list[] = 'Gagal menyimpan data. Silakan coba lagi atau hubungi admin.';
        }
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
        return view('livewire.utusan-dewan.pengajuan-kube');
    }
}
