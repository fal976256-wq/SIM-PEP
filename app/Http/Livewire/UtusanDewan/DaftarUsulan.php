<?php

namespace App\Http\Livewire\UtusanDewan;

use App\Models\UsulanPokir;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class DaftarUsulan extends Component
{
    use WithPagination;
    public $filterStatus = 'all';
    public $filterJenis = 'all';
    public $search = '';
    public $tahunPengajuan;
    public $perPage = 10;

    public function mount(): void
    {
        $this->tahunPengajuan = (int) date('Y');
    }

    public function updatedTahunPengajuan(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterJenis(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function getUsulanProperty()
    {
        $query = UsulanPokir::where('user_id', auth()->id())
            ->whereYear('created_at', $this->tahunPengajuan);

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterJenis !== 'all') {
            $query->where('jenis_bantuan', $this->filterJenis);
        }

        if ($this->search) {
            $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $this->search);
            $query->where(function ($q) use ($escaped) {
                $q->where('nama_kelompok_usaha', 'like', "%{$escaped}%", 'and')
                  ->orWhere('nama_ketua_individu', 'like', "%{$escaped}%", 'and')
                  ->orWhere('nik', 'like', "%{$escaped}%", 'and');
            });
        }

        return $query->latest()->paginate($this->perPage);
    }

    public function getStatusCounts(): array
    {
        $base = UsulanPokir::where('user_id', auth()->id())
            ->whereYear('created_at', $this->tahunPengajuan);

        $total = (clone $base)->count();

        $counts = (clone $base)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'all' => $total,
            'DRAFT' => $counts['DRAFT'] ?? 0,
            'REVIEW_DINAS' => $counts['REVIEW_DINAS'] ?? 0,
            'REVISI_UTUSAN' => $counts['REVISI_UTUSAN'] ?? 0,
            'CLEARED_RKA' => $counts['CLEARED_RKA'] ?? 0,
            'FINAL_APPROVED' => $counts['FINAL_APPROVED'] ?? 0,
        ];
    }

    public function getTahunList(): array
    {
        $min = UsulanPokir::where('user_id', auth()->id())->min('created_at');
        $current = (int) date('Y');
        $start = $min ? (int) \Carbon\Carbon::parse($min)->year : $current;
        return $start <= $current ? range($current, $start) : range($start, $current);
    }

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $query = UsulanPokir::where('user_id', auth()->id())
            ->whereYear('created_at', $this->tahunPengajuan);

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }
        if ($this->filterJenis !== 'all') {
            $query->where('jenis_bantuan', $this->filterJenis);
        }

        $usulan = $query->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="daftar-usulan-' . $this->tahunPengajuan . '.csv"',
        ];

        $callback = function () use ($usulan) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama', 'Jenis', 'NIK', 'Desil', 'Bidang Usaha', 'RAB', 'Status', 'Tanggal']);

            $no = 1;
            foreach ($usulan as $u) {
                fputcsv($file, [
                    $no++,
                    $u->nama_kelompok_usaha ?? $u->nama_ketua_individu,
                    $u->jenis_bantuan->value,
                    $u->nik,
                    $u->desil ?? '-',
                    $u->bidang_usaha ?? '-',
                    $u->total_rab ? number_format($u->total_rab, 0, ',', '.') : '-',
                    $u->status->label(),
                    $u->created_at->format('d/m/Y'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function submitUsulan($id): void
    {
        $usulan = UsulanPokir::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($usulan && $usulan->canEdit()) {
            $service = new \App\Services\UsulanService();
            $service->submitUsulan($usulan);
            session()->flash('success', 'Usulan berhasil disubmit untuk review!');
        }
    }

    public function render()
    {
        return view('livewire.utusan-dewan.daftar-usulan', [
            'usulanList' => $this->usulan,
            'statusCounts' => $this->getStatusCounts(),
            'tahunList' => $this->getTahunList(),
        ]);
    }
}
