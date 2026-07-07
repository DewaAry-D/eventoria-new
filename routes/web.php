<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Pages\Admin\{Dashboard, ModerasiEvent, EventDetail, ModerasiOrganisasi, OrganisasiDetail, AdminProfil};

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Grup 1: Mahasiswa
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Volt::route('/dashboard-mahasiswa', 'pages.mahasiswa.dashboard')->name('mahasiswa.dashboard');
    Volt::route('/mahasiswa/events/{event:slug}', 'pages.mahasiswa.event-detail')->name('mahasiswa.event-detail');
    Volt::route('/mahasiswa/events/{event:slug}/daftar', 'pages.mahasiswa.event-register')->name('mahasiswa.event-register');
    Volt::route('/mahasiswa/my-events', 'pages.mahasiswa.my-events')->name('mahasiswa.my-events');
    Volt::route('/mahasiswa/schedule', 'pages.mahasiswa.schedule')->name('mahasiswa.schedule');
    Volt::route('/mahasiswa/profil', 'pages.mahasiswa.profil')->name('mahasiswa.profil');
    Volt::route('/mahasiswa/sertifikat', 'pages.mahasiswa.sertifikat.index')->name('mahasiswa.sertifikat.index');
    Volt::route('/mahasiswa/sertifikat/{registration_id}', 'pages.mahasiswa.sertifikat.show')->name('mahasiswa.sertifikat.show');
    Route::get('/mahasiswa/sertifikat/{registration_id}/download', [App\Http\Controllers\SertifikatController::class, 'download'])->name('mahasiswa.sertifikat.download');
    Route::get('/mahasiswa/sertifikat/{registration_id}/download-jpg', [App\Http\Controllers\SertifikatController::class, 'downloadJpg'])->name('mahasiswa.sertifikat.download.jpg');
    Route::get('/mahasiswa/sertifikat/{registration_id}/download-jpg',
    [App\Http\Controllers\SertifikatController::class, 'downloadJpg']
    )->name('mahasiswa.sertifikat.download.jpg');
});

// Grup 2: Organisasi
Route::middleware(['auth', 'role:organisasi'])->group(function () {
    Volt::route('/dashboard-organisasi', 'pages.organisasi.dashboard')->name('organisasi.dashboard');
    Volt::route('/organisasi/events', 'pages.organisasi.events')->name('organisasi.events');
    Volt::route('/organisasi/events/create', 'pages.organisasi.event-create')->name('organisasi.events.create');
    Volt::route('/organisasi/events/{event}/edit', 'pages.organisasi.event-edit')->name('organisasi.events.edit');
    Volt::route('/organisasi/events/{event}/form-builder', 'pages.organisasi.event-form-builder')->name('organisasi.events.form-builder');
    Volt::route('/organisasi/events/{event}/sertifikat-builder', 'pages.organisasi.sertifikat-builder')->name('organisasi.events.sertifikat-builder');
    Volt::route('/organisasi/profil', 'pages.organisasi.profil')->name('organisasi.profil');
    Volt::route('/organisasi/events/{event}/peserta/{peserta}/jawaban', 'pages.organisasi.event-jawaban')->name('organisasi.events.jawaban');
    Volt::route('/events/{event}/pendaftar', 'pages.organisasi.kelola-pendaftaran')->name('organisasi.events.pendaftar');
});

// Grup 3: Admin DPM
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/profil', AdminProfil::class)->name('admin.profil');
    Route::get('/admin/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/admin/moderasi-event', ModerasiEvent::class)->name('admin.moderasi.event');
    Route::get('/admin/moderasi-event/detail/{event}', EventDetail::class)->name('admin.event.detail');
    Route::get('/admin/moderasi-organisasi', ModerasiOrganisasi::class)->name('admin.moderasi.organisasi');
    Route::get('/admin/moderasi-organisasi/detail/{id}', OrganisasiDetail::class)->name('admin.organisasi.detail');
});


// Rute Logout Standar
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';
