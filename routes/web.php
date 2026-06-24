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

    //organisasi
    Volt::route('/dashboard-organisasi', 'pages.organisasi.dashboard')->name('organisasi.dashboard');
    Volt::route('/organisasi/events', 'pages.organisasi.events')->name('organisasi.events');
    Volt::route('/organisasi/events/create', 'pages.organisasi.event-create')->name('organisasi.events.create');
    Volt::route('/organisasi/events/{event}/edit', 'pages.organisasi.event-edit')->name('organisasi.events.edit');
    Volt::route('/organisasi/events/{event}/form-builder', 'pages.organisasi.event-form-builder')->name('organisasi.events.form-builder');
    Volt::route('/organisasi/events/{event}/sertifikat-builder', 'pages.organisasi.sertifikat-builder')->name('organisasi.events.sertifikat-builder');
    Volt::route('/organisasi/profil', 'pages.organisasi.profil')->name('organisasi.profil');
    Volt::route('/events/{event}/pendaftar', 'pages.organisasi.kelola-pendaftaran')->name('organisasi.events.pendaftar');


    //admin
    Volt::route('/dashboard-admin', 'pages.admin.dashboard')->name('admin.dashboard');
    Volt::route('/moderasi-organisasi', 'pages.admin.moderasi-organisasi')->name('admin.moderasi-organisasi');
    Volt::route('/moderasi-organisasi/{id}', 'pages.admin.detail-organisasi')->name('admin.moderasi-organisasi.detail');
    Volt::route('/moderasi-event', 'pages.admin.moderasi-event')->name('admin.moderasi-event');
});



// Rute Logout Standar
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';
