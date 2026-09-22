<?php

namespace App\Http\Livewire\Verifikator;

use App\Enums\JenisBantuan;
use App\Enums\UsulanStatus;
use App\Models\UsulanPokir;
use Livewire\Component;

class LaporanArsip extends Component
{
    public $tahunPengajuan;
    public $filterJenis = 'all';
    public $filterStatus = 'all';

    public function mount(): void
    {
        $this->tahunPengajuan = (int) date('Y');
    }

    private function baseQuery()
    {
        $query = UsulanPokir::whereYear('created_at', $this->tahunPengajuan)
            ->with(['user', 'rekening']);

        if ($this->filterJenis !== 'all') {
            $query->where('jenis_bantuan', $this->filterJenis);
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        return $query;
    }

    public function getUsulanList()
    {
        return $this->baseQuery()->latest()->paginate(15);
    }

    public function getSummary(): array
    {
        $base = $this->baseQuery();

        return [
            'total' => (clone $base)->count(),
            'kube' => (clone $base)->where('jenis_bantuan', JenisBantuan::KUBE)->count(),
            'uep' => (clone $base)->where('jenis_bantuan', JenisBantuan::UEP)->count(),
            'approved' => (clone $base)->where('status', UsulanStatus::FINAL_APPROVED)->count(),
        ];
    }

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $usulan = $this->baseQuery()->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=laporan_usulan_{$this->tahunPengajuan}.csv",
        ];

        $callback = function () use ($usulan) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'No', 'Jenis_Bantuan', 'Tahun_Pengajuan', 'Tahun_Asal_Proposal', 'Tahun_Anggaran',
                'Nama_KUBE_Usaha', 'Nama_Ketua_Individu', 'NIK', 'Desil', 'Desil_Verified',
                'Kab', 'Kec', 'Desa', 'Alamat_Lengkap', 'Link_Google_Maps',
                'Bidang_Usaha', 'Total_RAB', 'No_Rekening_Bank', 'Status', 'Utusan_Dewan',
            ]);

            $no = 1;
            foreach ($usulan as $item) {
                fputcsv($file, [
                    $no++,
                    $item->jenis_bantuan->value,
                    $item->created_at->year,
                    $item->tahun_asal_proposal ?? '-',
                    $item->tahun_anggaran,
                    $item->nama_kelompok_usaha ?? '-',
                    $item->nama_ketua_individu,
                    $item->nik,
                    $item->desil ?? '-',
                    ($item->is_desil_valid ?? false) ? 'Ya' : 'Tidak',
                    $item->kabupaten ?? '-',
                    $item->kecamatan ?? '-',
                    $item->desa_kelurahan ?? '-',
                    $item->alamat_detail ?? $item->alamat_lengkap ?? '-',
                    $item->link_google_maps ?? '-',
                    $item->bidang_usaha ?? '-',
                    $item->total_rab ? number_format($item->total_rab, 0, ',', '.') : '-',
                    $item->rekening?->nomor_rekening ?? '-',
                    $item->status->label(),
                    $item->user?->name ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.verifikator.laporan-arsip', [
            'usulanList' => $this->getUsulanList(),
            'summary' => $this->getSummary(),
        ]);
    }
}
