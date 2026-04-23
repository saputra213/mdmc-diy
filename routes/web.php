<?php

use Illuminate\Support\Facades\Route;

// Public Routes (Masyarakat) - Tanpa Login
Route::livewire('/', 'public.landing')->name('landing');
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

    Route::livewire('/admin/bantuan-request', 'admin.bantuan-request.index')->name('bantuan-request.index');
});
