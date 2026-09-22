<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register Livewire Components
        Livewire::component('utusan-dewan.dashboard', \App\Http\Livewire\UtusanDewan\Dashboard::class);
        Livewire::component('utusan-dewan.pengajuan-kube', \App\Http\Livewire\UtusanDewan\PengajuanKube::class);
        Livewire::component('utusan-dewan.pengajuan-uep', \App\Http\Livewire\UtusanDewan\PengajuanUep::class);
        Livewire::component('utusan-dewan.daftar-usulan', \App\Http\Livewire\UtusanDewan\DaftarUsulan::class);

        Livewire::component('verifikator.dashboard', \App\Http\Livewire\Verifikator\Dashboard::class);
        Livewire::component('verifikator.kanban-board', \App\Http\Livewire\Verifikator\KanbanBoard::class);
        Livewire::component('verifikator.master-pagu', \App\Http\Livewire\Verifikator\MasterPagu::class);
        Livewire::component('verifikator.sinkronisasi-desil', \App\Http\Livewire\Verifikator\SinkronisasiDesil::class);
        Livewire::component('verifikator.laporan-arsip', \App\Http\Livewire\Verifikator\LaporanArsip::class);

        Livewire::component('admin-prov.dashboard', \App\Http\Livewire\AdminProv\Dashboard::class);
        Livewire::component('admin-prov.manajemen-user', \App\Http\Livewire\AdminProv\ManajemenUser::class);
        Livewire::component('admin-prov.gdrive-config', \App\Http\Livewire\AdminProv\GdriveConfig::class);
    }
}
