<?php

use App\Http\Livewire\AdminProv\Dashboard as AdminDashboard;
use App\Http\Livewire\AdminProv\ManajemenUser;
use App\Http\Livewire\UtusanDewan\Dashboard as UtusanDashboard;
use App\Http\Livewire\UtusanDewan\DaftarUsulan;
use App\Http\Livewire\UtusanDewan\PengajuanKube;
use App\Http\Livewire\UtusanDewan\PengajuanUep;
use App\Http\Livewire\Verifikator\Dashboard as VerifikatorDashboard;
use App\Http\Livewire\Verifikator\KanbanBoard;
use App\Http\Livewire\Verifikator\LaporanArsip;
use App\Http\Livewire\Verifikator\MasterPagu;
use App\Http\Livewire\Verifikator\SinkronisasiDesil;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->name('profile.')->group(function () {
    Route::get('/profile', fn() => view('profile'))->name('show');
});

/*
|--------------------------------------------------------------------------
| Dashboard - Role-based redirect
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = auth()->user();

    return match (true) {
        $user->isAdminProv() => redirect()->route('admin.dashboard'),
        $user->isVerifikator() => redirect()->route('verifikator.dashboard'),
        default => redirect()->route('utusan.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Utusan Dewan Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:UTUSAN_DEWAN'])->prefix('utusan')->name('utusan.')->group(function () {
    Route::get('/dashboard', fn() => view('pages.utusan-dewan.dashboard'))->name('dashboard');
    Route::get('/pengajuan/kube', fn() => view('pages.utusan-dewan.pengajuan-kube'))->name('pengajuan.kube');
    Route::get('/pengajuan/uep', fn() => view('pages.utusan-dewan.pengajuan-uep'))->name('pengajuan.uep');
    Route::get('/usulan-saya', fn() => view('pages.utusan-dewan.daftar-usulan'))->name('usulan-saya');
});

/*
|--------------------------------------------------------------------------
| Verifikator Dinas Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:VERIFIKATOR_DINAS,ADMIN_PROV'])->prefix('verifikator')->name('verifikator.')->group(function () {
    Route::get('/dashboard', fn() => view('pages.verifikator.dashboard'))->name('dashboard');
    Route::get('/verifikasi', fn() => view('pages.verifikator.kanban-board'))->name('verifikasi');
    Route::get('/master/pagu', fn() => view('pages.verifikator.master-pagu'))->name('master-pagu');
    Route::get('/master/desil', fn() => view('pages.verifikator.sinkronisasi-desil'))->name('master-desil');
    Route::get('/laporan', fn() => view('pages.verifikator.laporan-arsip'))->name('laporan');
});

/*
|--------------------------------------------------------------------------
| Admin Provinsi Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:ADMIN_PROV'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('pages.admin-prov.dashboard'))->name('dashboard');
    Route::get('/manajemen-user', fn() => view('pages.admin-prov.manajemen-user'))->name('manajemen-user');
    Route::get('/gdrive', fn() => view('pages.admin-prov.gdrive-config'))->name('gdrive');
});
