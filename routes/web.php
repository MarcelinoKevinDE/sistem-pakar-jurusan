<?php

use App\Http\Controllers\KarirController;
use Illuminate\Support\Facades\Route;

// Halaman utama (Form Input)
Route::get('/', [KarirController::class, 'index'])->name('jurusan.index');

// Halaman hasil (Analisis) - HANYA POST
Route::post('/hasil', [KarirController::class, 'analyze'])->name('jurusan.analyze');