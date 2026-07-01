<?php

namespace App\Http\Controllers;

use App\Services\ExpertSystem\KarirEngine;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KarirController extends Controller
{
    /**
     * Menampilkan halaman form kuisioner 10 pertanyaan.
     */
    public function index(): View
    {
        return view('index', [
            'pertanyaan' => KarirEngine::getPertanyaan(),
        ]);
    }

    /**
     * Memvalidasi jawaban, menjalankan KarirEngine, lalu menampilkan hasil.
     */
    public function analyze(Request $request): View
    {
        $totalSoal = count(KarirEngine::getPertanyaan());

        // Bangun aturan validasi secara dinamis: jawaban.1, jawaban.2, dst.
        $rules = [];
        $messages = [];

        for ($i = 1; $i <= $totalSoal; $i++) {
            $rules["jawaban.$i"] = 'required|in:a,b,c,d';
            $messages["jawaban.$i.required"] = "Pertanyaan nomor $i wajib dijawab.";
        }

        $validated = $request->validate($rules, $messages);

        $hasil = KarirEngine::proses($validated['jawaban']);

        return view('result', [
            'hasil' => $hasil,
        ]);
    }
}