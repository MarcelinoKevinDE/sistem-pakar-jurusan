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

// Fallback: kalau /hasil diakses via GET (misalnya karena refresh
// halaman hasil atau dibuka langsung dari address bar), arahkan
// kembali ke form agar tidak muncul error "Method Not Allowed".
Route::get('/hasil', function () {
    return redirect()->route('jurusan.index')
        ->with('info', 'Silakan isi kuisioner terlebih dahulu untuk melihat hasil rekomendasi.');
})->name('jurusan.hasil.fallback');