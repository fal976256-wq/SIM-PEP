<?php

namespace App\Http\Livewire\Verifikator;

use App\Models\MasterStandarPagu;
use Livewire\Component;

class MasterPagu extends Component
{
    public $tahunAnggaran;
    public $bidangUsaha = '';
    public $bidangUsahaLainnya = '';
    public $paguMaksimal = 0;
    public $editId = null;
    public $showForm = false;

    public $bidangUsahaList = [
        'Pertanian', 'Peternakan', 'Perikanan', 'Perdagangan',
        'Jasa', 'Industri Rumahan', 'Kerajinan Tangan', 'Lainnya',
    ];

    public function mount(): void
    {
        $this->tahunAnggaran = (int) date('Y');
    }

    public function getPaguList()
    {
        return MasterStandarPagu::where('tahun_anggaran', $this->tahunAnggaran)
            ->orderBy('bidang_usaha')
            ->get();
    }

    public function showCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id): void
    {
        $pagu = MasterStandarPagu::findOrFail($id);
        $this->editId = $id;
        $this->paguMaksimal = $pagu->pagu_maksimal;
        $this->showForm = true;

        if (in_array($pagu->bidang_usaha, $this->bidangUsahaList)) {
            $this->bidangUsaha = $pagu->bidang_usaha;
            $this->bidangUsahaLainnya = '';
        } else {
            $this->bidangUsaha = 'Lainnya';
            $this->bidangUsahaLainnya = $pagu->bidang_usaha;
        }
    }

    public function save(): void
    {
        if (empty($this->bidangUsaha) || empty($this->paguMaksimal)) {
            session()->flash('error', 'Bidang usaha dan pagu wajib diisi!');
            return;
        }

        $bidangUsaha = $this->bidangUsaha;
        if ($this->bidangUsaha === 'Lainnya') {
            if (empty($this->bidangUsahaLainnya)) {
                session()->flash('error', 'Bidang usaha Lainnya wajib diisi!');
                return;
            }
            $bidangUsaha = $this->bidangUsahaLainnya;
        }

        if ($this->paguMaksimal <= 0) {
            session()->flash('error', 'Pagu maksimal harus lebih dari 0!');
            return;
        }

        $existing = MasterStandarPagu::where('tahun_anggaran', $this->tahunAnggaran)
            ->where('bidang_usaha', $bidangUsaha)
            ->where('id', '!=', $this->editId)
            ->first();

        if ($existing) {
            session()->flash('error', 'Bidang usaha sudah ada untuk tahun ini!');
            return;
        }

        if ($this->editId) {
            MasterStandarPagu::findOrFail($this->editId)->update([
                'bidang_usaha' => $bidangUsaha,
                'pagu_maksimal' => $this->paguMaksimal,
            ]);
            session()->flash('success', 'Pagu berhasil diupdate!');
        } else {
            MasterStandarPagu::create([
                'tahun_anggaran' => $this->tahunAnggaran,
                'bidang_usaha' => $bidangUsaha,
                'pagu_maksimal' => $this->paguMaksimal,
            ]);
            session()->flash('success', 'Pagu berhasil ditambahkan!');
        }

        $this->resetForm();
    }

    public function delete($id): void
    {
        MasterStandarPagu::findOrFail($id)->delete();
        session()->flash('success', 'Pagu berhasil dihapus!');
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->bidangUsaha = '';
        $this->bidangUsahaLainnya = '';
        $this->paguMaksimal = 0;
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.verifikator.master-pagu', [
            'paguList' => $this->getPaguList(),
        ]);
    }
}
