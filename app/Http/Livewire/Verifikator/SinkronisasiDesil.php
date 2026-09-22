<?php

namespace App\Http\Livewire\Verifikator;

use App\Models\MasterDesilSen;
use Livewire\Component;
use Livewire\WithFileUploads;

class SinkronisasiDesil extends Component
{
    use WithFileUploads;

    public $tahunSync;
    public $csvFile = null;
    public $importCount = 0;
    public $search = '';
    public $filterDesil = '';

    public function mount(): void
    {
        $this->tahunSync = (int) date('Y');
    }

    public function getDesilList()
    {
        $query = MasterDesilSen::where('tahun_sync', $this->tahunSync);

        if ($this->search) {
            $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $this->search);
            $query->where(function ($q) use ($escaped) {
                $q->where('nik', 'like', "%{$escaped}%")
                  ->orWhere('nama', 'like', "%{$escaped}%")
                  ->orWhere('desa', 'like', "%{$escaped}%");
            });
        }

        if ($this->filterDesil !== '') {
            $query->where('desil', (int) $this->filterDesil);
        }

        return $query->orderBy('desil')->paginate(20);
    }

    public function importCsv(): void
    {
        if (!$this->csvFile) {
            session()->flash('error', 'Pilih file CSV terlebih dahulu!');
            return;
        }

        $file = $this->csvFile;

        // Validate MIME type
        $allowedMimes = ['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            session()->flash('error', 'Tipe file tidak diizinkan. Hanya file CSV yang diperbolehkan.');
            return;
        }

        // Validate extension
        $extension = strtolower($file->getClientOriginalExtension());
        if ($extension !== 'csv') {
            session()->flash('error', 'Ekstensi file harus .csv');
            return;
        }

        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        // Expected headers: NIK,Nama,Desil,Alamat,Desa,Kecamatan,Kabupaten
        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 7) continue;

            [$nik, $nama, $desil, $alamat, $desa, $kecamatan, $kabupaten] = $row;

            MasterDesilSen::updateOrCreate(
                ['nik' => $nik, 'tahun_sync' => $this->tahunSync],
                [
                    'nama' => $nama,
                    'desil' => (int) $desil,
                    'alamat' => $alamat,
                    'desa' => $desa,
                    'kecamatan' => $kecamatan,
                    'kabupaten' => $kabupaten,
                ]
            );

            $count++;
        }

        fclose($handle);

        $this->importCount = $count;
        $this->csvFile = null;
        session()->flash('success', "Berhasil import {$count} data DTSEN!");
    }

    public function render()
    {
        return view('livewire.verifikator.sinkronisasi-desil', [
            'desilList' => $this->getDesilList(),
        ]);
    }
}
