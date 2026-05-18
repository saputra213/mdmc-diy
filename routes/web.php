<?php

use Illuminate\Support\Facades\Route;

// Public Routes (Masyarakat) - Tanpa Login
Route::livewire('/', 'public.landing')->name('landing');
Route::livewire('/profil', 'public.profil')->name('profil');
Route::livewire('/layanan', 'public.layanan')->name('layanan');
Route::livewire('/berita', 'public.berita')->name('berita');
Route::livewire('/galeri', 'public.galeri')->name('galeri');
Route::livewire('/galeri/{eventId}', 'public.galeri')->name('galeri.event');
Route::livewire('/donasi', 'public.donasi')->name('donasi');
Route::livewire('/history-bencana', 'public.disaster-history')->name('public.disaster-history');
Route::livewire('/history-bencana/{id}', 'public.disaster-history-show')->name('public.disaster-history-show');
Route::livewire('/video', 'public.video.index')->name('video.index');
Route::livewire('/video/{id}', 'public.video.show')->name('video.show');
Route::livewire('/belajar-bisindo', 'public.belajar-bisindo')->name('belajar-bisindo');
Route::livewire('/login', 'public.login')->name('login');
Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');
Route::livewire('/request-bantuan', 'public.public-request')->name('public-request');

// Admin Routes (Internal) - Harus Login & Role Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::livewire('/dashboard', 'admin.dashboard')->name('dashboard');

    Route::livewire('/disaster-events', 'admin.disaster-event.index')->name('admin.disaster-events.index');
    Route::livewire('/disaster-events/history', 'admin.disaster-event.history')->name('admin.disaster-events.history');
    
    // Donatur
    Route::livewire('/donatur', 'admin.donatur.index')->name('donatur.index');
    Route::livewire('/donatur/create', 'admin.donatur.create')->name('donatur.create');
    Route::livewire('/donatur/edit/{id}', 'admin.donatur.edit')->name('donatur.edit');

    // Logistik (Master)
    Route::livewire('/barang', 'admin.logistik.index')->name('barang.index');
    Route::livewire('/barang/create', 'admin.logistik.create')->name('barang.create');
    Route::livewire('/barang/edit/{id}', 'admin.logistik.edit')->name('barang.edit');

    // Lokasi
    Route::livewire('/lokasi', 'admin.lokasi.index')->name('lokasi.index');
    Route::livewire('/lokasi/create', 'admin.lokasi.create')->name('lokasi.create');
    Route::livewire('/lokasi/edit/{id}', 'admin.lokasi.edit')->name('lokasi.edit');

    // Manajemen Logistik
    Route::livewire('/barang-masuk', 'admin.logistik-masuk.index')->name('barang-masuk.index');
    Route::livewire('/barang-masuk/create', 'admin.logistik-masuk.create')->name('barang-masuk.create');
    
    Route::livewire('/barang-keluar', 'admin.logistik-keluar.index')->name('barang-keluar.index');
    Route::livewire('/barang-keluar/create', 'admin.logistik-keluar.create')->name('barang-keluar.create');

    Route::livewire('/kebutuhan', 'admin.kebutuhan.index')->name('kebutuhan.index');
    Route::livewire('/kebutuhan/create', 'admin.kebutuhan.create')->name('kebutuhan.create');
    Route::livewire('/kebutuhan/edit/{id}', 'admin.kebutuhan.edit')->name('kebutuhan.edit');

    Route::livewire('/laporan', 'admin.laporan.index')->name('laporan.index');
    Route::get('/laporan/print', function() {
        return view('admin.laporan.print');
    })->name('admin.laporan.print');

    Route::livewire('/user', 'admin.user.index')->name('user.index');
    Route::livewire('/user/create', 'admin.user.create')->name('user.create');
    Route::livewire('/user/edit/{id}', 'admin.user.edit')->name('user.edit');

    Route::livewire('/settings', 'admin.settings.index')->name('settings.index');
    Route::livewire('/settings/profil', 'admin.settings.profil')->name('settings.profil');
    Route::livewire('/settings/layanan', 'admin.settings.layanan')->name('settings.layanan');
    Route::livewire('/settings/berita', 'admin.settings.berita')->name('settings.berita');
    Route::livewire('/settings/galeri', 'admin.settings.galeri')->name('settings.galeri');
    Route::livewire('/settings/donasi', 'admin.settings.donasi')->name('settings.donasi');

    Route::livewire('/admin/video', 'admin.video.index')->name('admin.video.index');
    Route::livewire('/admin/video/create', 'admin.video.create')->name('admin.video.create');
    Route::livewire('/admin/video/edit/{id}', 'admin.video.edit')->name('admin.video.edit');

    Route::livewire('/admin/bisindo', 'admin.bisindo.index')->name('admin.bisindo.index');
    Route::livewire('/admin/bisindo/create', 'admin.bisindo.create')->name('admin.bisindo.create');
    Route::livewire('/admin/bisindo/edit/{id}', 'admin.bisindo.edit')->name('admin.bisindo.edit');

    Route::livewire('/admin/bantuan-request', 'admin.bantuan-request.index')->name('bantuan-request.index');
});
