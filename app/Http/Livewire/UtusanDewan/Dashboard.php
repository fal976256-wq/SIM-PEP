<?php

namespace App\Http\Livewire\UtusanDewan;

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
        $userId = auth()->id();
        $service = app(\App\Services\UsulanService::class);
        $this->stats = $service->getStats($this->tahunPengajuan, $userId);

        $this->statusCounts = UsulanPokir::where('user_id', $userId)
            ->whereYear('created_at', $this->tahunPengajuan)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $this->recentUsulan = UsulanPokir::where('user_id', $userId)
            ->whereYear('created_at', $this->tahunPengajuan)
            ->latest()
            ->take(config('app.dashboard_recent_limit'))
            ->get();
    }

    public function render()
    {
        return view('livewire.utusan-dewan.dashboard');
    }
}
