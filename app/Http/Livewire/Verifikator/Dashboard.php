<?php

namespace App\Http\Livewire\Verifikator;

use App\Models\UsulanPokir;
use Livewire\Component;

class Dashboard extends Component
{
    public $stats = [];
    public $statusCounts = [];
    public $recentUsulan = [];
    public $tahunPengajuan;
    public $tahunOptions = [];

    public function mount(): void
    {
        $this->tahunPengajuan = (int) date('Y');
        $this->tahunOptions = range(2026, 2030);
        $this->loadData();
    }

    public function updatedTahunPengajuan(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $service = app(\App\Services\UsulanService::class);
        $this->stats = $service->getStats($this->tahunPengajuan);

        $this->statusCounts = UsulanPokir::whereYear('created_at', $this->tahunPengajuan)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $this->recentUsulan = UsulanPokir::whereYear('created_at', $this->tahunPengajuan)
            ->with('user')
            ->latest()
            ->take(config('app.dashboard_recent_limit'))
            ->get();
    }

    public function render()
    {
        return view('livewire.verifikator.dashboard');
    }
}
