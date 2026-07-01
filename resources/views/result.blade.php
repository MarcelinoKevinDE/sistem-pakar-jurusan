<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Rekomendasi Jurusan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream: '#FFF7E6',
                        pinkbrutal: '#FF6FA0',
                        pinklight: '#FFD1E3',
                        yellowbrutal: '#FFE159',
                    },
                    boxShadow: {
                        brutal: '8px 8px 0px 0px #000000',
                        brutalsm: '4px 4px 0px 0px #000000',
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-cream min-h-screen p-4 md:p-8">

    <div class="max-w-3xl mx-auto">

        <div class="bg-yellowbrutal border-4 border-black shadow-brutal p-4 mb-8 inline-block rotate-1">
            <h1 class="text-2xl md:text-4xl font-black uppercase tracking-tight">📊 Hasil Rekomendasi Jurusan</h1>
        </div>

        <p class="mb-8 font-bold bg-white border-4 border-black shadow-brutalsm p-4">
            Berdasarkan jawabanmu, berikut 3 kategori jurusan yang paling sesuai dengan minat dan kepribadianmu,
            diurutkan dari yang paling cocok.
        </p>

        @php
            $medali = ['🥇', '🥈', '🥉'];
            $warnaBadge = ['bg-yellowbrutal', 'bg-pinklight', 'bg-white'];
        @endphp

        <div class="space-y-6 mb-8">
            @foreach ($hasil['rekomendasi'] as $index => $rekom)
                <div class="bg-white border-4 border-black shadow-brutal p-6">

                    {{-- Header kartu --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">{{ $medali[$index] ?? '🎯' }}</span>
                            <h2 class="text-xl md:text-2xl font-black uppercase">{{ $rekom['nama'] }}</h2>
                        </div>
                        <span class="{{ $warnaBadge[$index] ?? 'bg-white' }} border-4 border-black px-3 py-1 font-black text-sm shrink-0">
                            {{ $rekom['persentase'] }}% Cocok
                        </span>
                    </div>

                    {{-- Progress bar kecocokan --}}
                    <div class="border-4 border-black h-6 w-full bg-cream mb-4 overflow-hidden">
                        <div class="h-full bg-pinkbrutal border-r-4 border-black" style="width: {{ $rekom['persentase'] }}%"></div>
                    </div>

                    <p class="font-medium mb-4">{{ $rekom['deskripsi'] }}</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="border-4 border-black p-3 bg-cream">
                            <p class="font-black uppercase text-xs mb-2">🎓 Jurusan Terkait</p>
                            <ul class="text-sm space-y-1 font-semibold">
                                @foreach ($rekom['jurusan'] as $j)
                                    <li>• {{ $j }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="border-4 border-black p-3 bg-cream">
                            <p class="font-black uppercase text-xs mb-2">📚 Mata Kuliah Inti</p>
                            <ul class="text-sm space-y-1 font-semibold">
                                @foreach ($rekom['mata_kuliah'] as $mk)
                                    <li>• {{ $mk }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="border-4 border-black p-3 bg-cream">
                            <p class="font-black uppercase text-xs mb-2">💼 Prospek Karier</p>
                            <ul class="text-sm space-y-1 font-semibold">
                                @foreach ($rekom['prospek'] as $p)
                                    <li>• {{ $p }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <a href="{{ route('jurusan.index') }}"
           class="inline-block bg-white border-4 border-black shadow-brutalsm hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all font-black uppercase px-6 py-3">
            ⬅ Ulangi Kuisioner
        </a>

    </div>

</body>
</html>