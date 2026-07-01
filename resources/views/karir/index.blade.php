<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar Pemilihan Jurusan Kuliah</title>
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

        {{-- HEADER --}}
        <div class="bg-yellowbrutal border-4 border-black shadow-brutal p-4 mb-8 inline-block -rotate-1">
            <h1 class="text-2xl md:text-4xl font-black uppercase tracking-tight">🎓 Sistem Pakar Jurusan Kuliah</h1>
        </div>

        <p class="mb-8 font-bold bg-white border-4 border-black shadow-brutalsm p-4">
            Jawab 10 pertanyaan berikut dengan jujur. Sistem akan menganalisis minat &amp; kepribadianmu
            lalu memberikan rekomendasi jurusan kuliah yang paling sesuai.
        </p>

        {{-- ERROR MESSAGES --}}
        @if ($errors->any())
            <div class="bg-white border-4 border-black shadow-brutal p-4 mb-8">
                <p class="font-black uppercase mb-2">⚠️ Ada pertanyaan yang belum dijawab:</p>
                <ul class="list-disc list-inside font-semibold text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('jurusan.analyze') }}" method="POST" class="space-y-6">
            @csrf

            @foreach ($pertanyaan as $nomor => $soal)
                <div class="bg-white border-4 border-black shadow-brutal p-5">

                    <div class="flex items-start gap-3 mb-4">
                        <span class="bg-pinkbrutal border-4 border-black font-black w-10 h-10 flex items-center justify-center shrink-0 text-lg">
                            {{ $nomor }}
                        </span>
                        <p class="font-black text-lg leading-snug pt-1">{{ $soal['teks'] }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($soal['pilihan'] as $kunci => $opsi)
                            <label class="border-4 border-black p-3 font-semibold text-sm cursor-pointer hover:bg-pinklight has-[:checked]:bg-pinkbrutal has-[:checked]:font-black transition-colors flex items-center gap-2">
                                <input
                                    type="radio"
                                    name="jawaban[{{ $nomor }}]"
                                    value="{{ $kunci }}"
                                    class="w-4 h-4 accent-black shrink-0"
                                    {{ old("jawaban.$nomor") == $kunci ? 'checked' : '' }}
                                    required
                                >
                                <span>{{ $opsi['label'] }}</span>
                            </label>
                        @endforeach
                    </div>

                </div>
            @endforeach

            <button type="submit"
                    class="w-full bg-pinkbrutal border-4 border-black shadow-brutal hover:shadow-none hover:translate-x-2 hover:translate-y-2 transition-all font-black uppercase py-4 text-xl">
                🚀 Lihat Rekomendasi Jurusan
            </button>

        </form>

    </div>

</body>
</html>