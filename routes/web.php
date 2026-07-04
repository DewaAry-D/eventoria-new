<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


// Rute Sementara untuk menghindari error setelah Login/Register
Route::middleware('auth')->group(function () {
    Volt::route('/dashboard-mahasiswa', 'pages.mahasiswa.dashboard')->name('mahasiswa.dashboard');
    Volt::route('/mahasiswa/profil', 'pages.mahasiswa.profil')->name('mahasiswa.profil');
    Volt::route('/mahasiswa/sertifikat', 'pages.mahasiswa.sertifikat.index')->name('mahasiswa.sertifikat.index');
    Volt::route('/mahasiswa/sertifikat/{registration_id}', 'pages.mahasiswa.sertifikat.show')->name('mahasiswa.sertifikat.show');
    Route::get('/mahasiswa/sertifikat/{registration_id}/download', [App\Http\Controllers\SertifikatController::class, 'download'])->name('mahasiswa.sertifikat.download');
    Route::get('/mahasiswa/sertifikat/{registration_id}/download-jpg', [App\Http\Controllers\SertifikatController::class, 'downloadJpg'])->name('mahasiswa.sertifikat.download.jpg');
    Route::get('/mahasiswa/sertifikat/{registration_id}/download-jpg',
    [App\Http\Controllers\SertifikatController::class, 'downloadJpg']
    )->name('mahasiswa.sertifikat.download.jpg');

    //organisasi
    Volt::route('/dashboard-organisasi', 'pages.organisasi.dashboard')->name('organisasi.dashboard');
    Volt::route('/organisasi/events', 'pages.organisasi.events')->name('organisasi.events');
    Volt::route('/organisasi/events/create', 'pages.organisasi.event-create')->name('organisasi.events.create');
    Volt::route('/organisasi/events/{event}/edit', 'pages.organisasi.event-edit')->name('organisasi.events.edit');
    Volt::route('/organisasi/events/{event}/form-builder', 'pages.organisasi.event-form-builder')->name('organisasi.events.form-builder');
    Volt::route('/organisasi/events/{event}/sertifikat-builder', 'pages.organisasi.sertifikat-builder')->name('organisasi.events.sertifikat-builder');
    Volt::route('/organisasi/profil', 'pages.organisasi.profil')->name('organisasi.profil');


    //admin
    Volt::route('/dashboard-admin', 'pages.admin.dashboard')->name('admin.dashboard');
});



// Rute Logout Standar
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';
