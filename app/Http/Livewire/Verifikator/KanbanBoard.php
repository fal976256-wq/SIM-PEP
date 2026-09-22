<?php

namespace App\Http\Livewire\Verifikator;

use App\Enums\JenisBantuan;
use App\Enums\UsulanStatus;
use App\Models\UsulanPokir;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class KanbanBoard extends Component
{
    public $activeTab = 'KUBE';
    public $selectedUsulanId = null;
    public $catatan = '';
    public $filterStatus = 'all';

    // Split pane
    public $showDetail = false;

    // Per-column pagination
    public $columnLimit = 10;
    public $columnIncrements = [];

    public function mount(): void
    {
        $this->selectedUsulanId = null;
        $this->loadColumns();
    }

    public function selectUsulan($id): void
    {
        $this->selectedUsulanId = $id;
        $this->showDetail = true;
        $this->catatan = '';
        $this->selectedUsulan = UsulanPokir::with(['dokumen', 'rekening', 'user', 'anggota', 'anggotaDewan'])->find($id);
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedUsulanId = null;
        $this->catatan = '';
        $this->selectedUsulan = null;
    }

    public function loadMoreColumn(string $column): void
    {
        $this->columnIncrements[$column] = ($this->columnIncrements[$column] ?? 0) + 10;
    }

    public function getColumnLimit(string $column): int
    {
        return $this->columnLimit + ($this->columnIncrements[$column] ?? 0);
    }

    public function loadData(): void
    {
        $this->loadColumns();
        $this->selectedUsulanId = null;
        $this->showDetail = false;
        $this->catatan = '';
        $this->selectedUsulan = null;
    }

    public array $kubeColumns = [];
    public array $uepColumns = [];
    public ?\App\Models\UsulanPokir $selectedUsulan = null;

    public function loadColumns(): void
    {
        $kubeBase = UsulanPokir::where('jenis_bantuan', JenisBantuan::KUBE)
            ->with(['user', 'dokumen', 'anggotaDewan']);
        $this->kubeColumns = [
            'review' => (clone $kubeBase)->where('status', UsulanStatus::REVIEW_DINAS)->latest()->get(),
            'revisi' => (clone $kubeBase)->where('status', UsulanStatus::REVISI_UTUSAN)->latest()->get(),
            'cleared' => (clone $kubeBase)->where('status', UsulanStatus::CLEARED_RKA)->latest()->get(),
        ];

        $uepBase = UsulanPokir::where('jenis_bantuan', JenisBantuan::UEP)
            ->with(['user', 'rekening', 'dokumen', 'anggotaDewan']);
        $this->uepColumns = [
            'review' => (clone $uepBase)->where('status', UsulanStatus::REVIEW_DINAS)->latest()->get(),
            'revisi' => (clone $uepBase)->where('status', UsulanStatus::REVISI_UTUSAN)->latest()->get(),
            'cleared' => (clone $uepBase)->where('status', UsulanStatus::CLEARED_RKA)->latest()->get(),
        ];
    }

    public function approve($id): void
    {
        $this->authorizeVerifikator();

        DB::transaction(function () use ($id) {
            $usulan = UsulanPokir::lockForUpdate()->find($id);
            if ($usulan && $usulan->canBeVerified()) {
                $service = app(\App\Services\UsulanService::class);
                $service->approveUsulan($usulan, $this->catatan ?: null);
            }
        });

        $this->closeDetail();
        $this->loadColumns();
        session()->flash('success', 'Usulan #' . $id . ' berhasil di-Clear RKA!');
    }

    public function returnWithNote($id): void
    {
        $this->authorizeVerifikator();

        if (empty($this->catatan)) {
            session()->flash('error', 'Catatan revisi wajib diisi!');
            return;
        }

        DB::transaction(function () use ($id) {
            $usulan = UsulanPokir::lockForUpdate()->find($id);
            if ($usulan && $usulan->canBeVerified()) {
                $service = app(\App\Services\UsulanService::class);
                $service->returnUsulan($usulan, $this->catatan);
            }
        });

        $this->closeDetail();
        $this->loadColumns();
        session()->flash('success', 'Usulan #' . $id . ' dikembalikan untuk revisi.');
    }

    public function finalApprove($id): void
    {
        $this->authorizeVerifikator();

        DB::transaction(function () use ($id) {
            $usulan = UsulanPokir::lockForUpdate()->find($id);
            if ($usulan && $usulan->status === UsulanStatus::CLEARED_RKA) {
                $service = app(\App\Services\UsulanService::class);
                $service->finalApprove($usulan);
            }
        });

        $this->closeDetail();
        $this->loadColumns();
        session()->flash('success', 'Usulan #' . $id . ' Final Disetujui!');
    }

    private function authorizeVerifikator(): void
    {
        abort_unless(
            in_array(auth()->user()?->role?->value, ['VERIFIKATOR_DINAS', 'ADMIN_PROV']),
            403,
            'Anda tidak memiliki akses untuk melakukan aksi ini.'
        );
    }

    public function render()
    {
        return view('livewire.verifikator.kanban-board');
    }
}
