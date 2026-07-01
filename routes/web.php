<?php

use App\Http\Controllers\KarirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Sistem Pakar Pemilihan Jurusan Kuliah
|--------------------------------------------------------------------------
*/

Route::get('/', [KarirController::class, 'index'])->name('jurusan.index');
Route::post('/hasil', [KarirController::class, 'analyze'])->name('jurusan.analyze');