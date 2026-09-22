<?php

namespace App\Http\Livewire\AdminProv;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\UsulanPokir;
use Livewire\Component;

class Dashboard extends Component
{
    public $stats = [];
    public $statusCounts = [];
    public $recentUsulan = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $service = app(\App\Services\UsulanService::class);
        $this->stats = array_merge(
            $service->getStats((int) date('Y')),
            [
                'total_users' => User::count(),
                'utusan_dewan' => User::where('role', UserRole::UTUSAN_DEWAN)->count(),
                'verifikator' => User::where('role', UserRole::VERIFIKATOR_DINAS)->count(),
                'admin' => User::where('role', UserRole::ADMIN_PROV)->count(),
            ]
        );

        $this->statusCounts = UsulanPokir::whereYear('created_at', (int) date('Y'))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $this->recentUsulan = UsulanPokir::with('user')
            ->whereYear('created_at', (int) date('Y'))
            ->latest()
            ->take(config('app.dashboard_recent_limit'))
            ->get();
    }

    public function render()
    {
        return view('livewire.admin-prov.dashboard', [
            'recentUsulan' => $this->recentUsulan ?? collect(),
        ]);
    }
}
