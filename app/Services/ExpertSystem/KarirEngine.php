<?php

namespace App\Services\ExpertSystem;

/**
 * KarirEngine
 *
 * Sistem pakar berbasis rules (rule-based expert system) untuk
 * merekomendasikan jurusan kuliah berdasarkan jawaban kuisioner.
 *
 * Metode inferensi: forward chaining sederhana dengan sistem skor (scoring).
 * Setiap pilihan jawaban memberi poin ke satu atau lebih "kategori jurusan".
 * Kategori dengan skor tertinggi menjadi rekomendasi utama.
 *
 * Tidak menggunakan database — seluruh basis pengetahuan (knowledge base)
 * disimpan sebagai konstanta di dalam class ini.
 */
class KarirEngine
{
    /**
     * Basis pengetahuan kategori jurusan.
     * key => [nama, deskripsi, jurusan terkait, mata kuliah inti, prospek karir]
     */
    public const KATEGORI = [
        'teknik' => [
            'nama' => 'Teknik & Informatika',
            'deskripsi' => 'Kamu cenderung logis, sistematis, dan senang memecahkan masalah teknis. Cocok untuk bidang yang mengandalkan logika, eksperimen, dan penciptaan solusi teknologi.',
            'jurusan' => ['Teknik Informatika', 'Ilmu Komputer', 'Teknik Elektro', 'Sistem Informasi', 'Teknik Industri'],
            'mata_kuliah' => ['Algoritma & Pemrograman', 'Matematika Diskrit', 'Jaringan Komputer', 'Basis Data'],
            'prospek' => ['Software Engineer', 'Data Scientist', 'Network Engineer', 'IT Consultant'],
        ],
        'kesehatan' => [
            'nama' => 'Kedokteran & Kesehatan',
            'deskripsi' => 'Kamu memiliki ketertarikan tinggi pada ilmu hayati dan kepedulian terhadap kesehatan orang lain. Cocok untuk bidang yang menuntut ketelitian dan empati.',
            'jurusan' => ['Pendidikan Dokter', 'Farmasi', 'Keperawatan', 'Gizi', 'Kesehatan Masyarakat'],
            'mata_kuliah' => ['Anatomi', 'Fisiologi', 'Biokimia', 'Farmakologi'],
            'prospek' => ['Dokter', 'Apoteker', 'Perawat', 'Ahli Gizi'],
        ],
        'bisnis' => [
            'nama' => 'Ekonomi & Bisnis',
            'deskripsi' => 'Kamu berpikir strategis, suka menganalisis angka dan peluang. Cocok untuk bidang yang berkaitan dengan pengelolaan usaha dan keuangan.',
            'jurusan' => ['Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Bisnis Digital'],
            'mata_kuliah' => ['Akuntansi Dasar', 'Manajemen Keuangan', 'Ekonomi Mikro', 'Pemasaran'],
            'prospek' => ['Business Analyst', 'Akuntan', 'Financial Planner', 'Entrepreneur'],
        ],
        'sosial' => [
            'nama' => 'Ilmu Sosial & Politik',
            'deskripsi' => 'Kamu peka terhadap isu masyarakat dan senang berdiskusi tentang dinamika sosial-politik. Cocok untuk bidang yang mengkaji hubungan antar manusia dan negara.',
            'jurusan' => ['Ilmu Politik', 'Hubungan Internasional', 'Sosiologi', 'Antropologi'],
            'mata_kuliah' => ['Teori Sosial', 'Politik Global', 'Metodologi Penelitian Sosial'],
            'prospek' => ['Diplomat', 'Peneliti Sosial', 'Analis Kebijakan', 'Jurnalis'],
        ],
        'psikologi' => [
            'nama' => 'Psikologi',
            'deskripsi' => 'Kamu memiliki empati tinggi dan tertarik memahami pikiran serta perilaku manusia. Cocok untuk bidang yang berfokus pada individu dan hubungan interpersonal.',
            'jurusan' => ['Psikologi'],
            'mata_kuliah' => ['Psikologi Perkembangan', 'Psikologi Klinis', 'Psikometri'],
            'prospek' => ['Psikolog', 'HR Specialist', 'Konselor', 'Peneliti Perilaku'],
        ],
        'desain' => [
            'nama' => 'Desain & Seni',
            'deskripsi' => 'Kamu kreatif, punya kepekaan visual, dan senang menciptakan karya. Cocok untuk bidang yang mengandalkan estetika dan ekspresi kreatif.',
            'jurusan' => ['Desain Komunikasi Visual', 'Desain Produk', 'Film & Televisi', 'Seni Rupa'],
            'mata_kuliah' => ['Tipografi', 'Ilustrasi Digital', 'Videografi'],
            'prospek' => ['Graphic Designer', 'UI/UX Designer', 'Animator', 'Content Creator'],
        ],
        'hukum' => [
            'nama' => 'Ilmu Hukum',
            'deskripsi' => 'Kamu berpikir kritis, senang berargumen berdasarkan aturan, dan tertarik pada keadilan. Cocok untuk bidang yang berkaitan dengan regulasi dan advokasi.',
            'jurusan' => ['Ilmu Hukum'],
            'mata_kuliah' => ['Hukum Pidana', 'Hukum Perdata', 'Hukum Tata Negara'],
            'prospek' => ['Pengacara', 'Notaris', 'Legal Officer', 'Hakim'],
        ],
        'pendidikan' => [
            'nama' => 'Pendidikan',
            'deskripsi' => 'Kamu senang berbagi ilmu dan sabar membimbing orang lain. Cocok untuk bidang yang berfokus pada pengajaran dan pengembangan kurikulum.',
            'jurusan' => ['Pendidikan Guru', 'Manajemen Pendidikan', 'Bimbingan Konseling'],
            'mata_kuliah' => ['Psikologi Pendidikan', 'Kurikulum & Pembelajaran'],
            'prospek' => ['Guru', 'Dosen', 'Konsultan Pendidikan'],
        ],
        'bahasa' => [
            'nama' => 'Sastra & Bahasa',
            'deskripsi' => 'Kamu punya kepekaan terhadap kata dan bahasa, serta senang membaca atau menulis. Cocok untuk bidang yang mendalami linguistik dan karya sastra.',
            'jurusan' => ['Sastra Inggris', 'Sastra Indonesia', 'Linguistik', 'Pendidikan Bahasa'],
            'mata_kuliah' => ['Linguistik Umum', 'Kajian Sastra', 'Penerjemahan'],
            'prospek' => ['Penerjemah', 'Penulis', 'Content Writer', 'Editor'],
        ],
        'pertanian' => [
            'nama' => 'Pertanian & Lingkungan',
            'deskripsi' => 'Kamu tertarik pada alam, makhluk hidup, dan keberlanjutan lingkungan. Cocok untuk bidang yang berhubungan dengan sumber daya alam.',
            'jurusan' => ['Agroteknologi', 'Kehutanan', 'Ilmu Lingkungan', 'Peternakan'],
            'mata_kuliah' => ['Ilmu Tanah', 'Ekologi', 'Budidaya Tanaman'],
            'prospek' => ['Ahli Agronomi', 'Peneliti Lingkungan', 'Konsultan Pertanian'],
        ],
    ];

    /**
     * Basis aturan (rules) pertanyaan kuisioner.
     * Setiap pilihan (a/b/c/d) memetakan poin ke satu/lebih kategori.
     */
    public const PERTANYAAN = [
        1 => [
            'teks' => 'Mata pelajaran apa yang paling kamu sukai di sekolah?',
            'pilihan' => [
                'a' => ['label' => 'Matematika & Fisika', 'poin' => ['teknik' => 3]],
                'b' => ['label' => 'Biologi & Kimia', 'poin' => ['kesehatan' => 3, 'pertanian' => 1]],
                'c' => ['label' => 'Ekonomi & Akuntansi', 'poin' => ['bisnis' => 3]],
                'd' => ['label' => 'Bahasa Indonesia / Bahasa Inggris', 'poin' => ['bahasa' => 3]],
            ],
        ],
        2 => [
            'teks' => 'Kegiatan apa yang paling kamu nikmati di waktu luang?',
            'pilihan' => [
                'a' => ['label' => 'Coding atau otak-atik gadget', 'poin' => ['teknik' => 3]],
                'b' => ['label' => 'Menulis cerita atau membaca novel', 'poin' => ['bahasa' => 3]],
                'c' => ['label' => 'Menggambar, desain, atau edit video', 'poin' => ['desain' => 3]],
                'd' => ['label' => 'Ikut diskusi atau organisasi sosial', 'poin' => ['sosial' => 3]],
            ],
        ],
        3 => [
            'teks' => 'Menurutmu, kekuatan terbesarmu adalah?',
            'pilihan' => [
                'a' => ['label' => 'Analitis dan suka memecahkan masalah logika', 'poin' => ['teknik' => 2, 'hukum' => 1]],
                'b' => ['label' => 'Empati tinggi, senang membantu orang', 'poin' => ['psikologi' => 3]],
                'c' => ['label' => 'Kreatif dan punya banyak ide visual', 'poin' => ['desain' => 3]],
                'd' => ['label' => 'Persuasif dan pandai berkomunikasi', 'poin' => ['bisnis' => 2, 'hukum' => 2]],
            ],
        ],
        4 => [
            'teks' => 'Dalam proyek kelompok, kamu biasanya berperan sebagai?',
            'pilihan' => [
                'a' => ['label' => 'Yang merancang sistem/hal teknis', 'poin' => ['teknik' => 3]],
                'b' => ['label' => 'Yang mengatur strategi & anggaran', 'poin' => ['bisnis' => 3]],
                'c' => ['label' => 'Yang menjadi juru bicara/negosiator', 'poin' => ['hukum' => 3]],
                'd' => ['label' => 'Yang mendesain tampilan/presentasi', 'poin' => ['desain' => 3]],
            ],
        ],
        5 => [
            'teks' => 'Topik berita apa yang paling menarik perhatianmu?',
            'pilihan' => [
                'a' => ['label' => 'Teknologi & inovasi AI', 'poin' => ['teknik' => 3]],
                'b' => ['label' => 'Kesehatan & penemuan medis', 'poin' => ['kesehatan' => 3]],
                'c' => ['label' => 'Ekonomi & pasar saham', 'poin' => ['bisnis' => 3]],
                'd' => ['label' => 'Hukum & kebijakan publik', 'poin' => ['hukum' => 3]],
            ],
        ],
        6 => [
            'teks' => 'Gaya belajar apa yang paling cocok untukmu?',
            'pilihan' => [
                'a' => ['label' => 'Praktik & eksperimen langsung', 'poin' => ['kesehatan' => 2, 'teknik' => 2]],
                'b' => ['label' => 'Diskusi & debat', 'poin' => ['sosial' => 2, 'hukum' => 2]],
                'c' => ['label' => 'Visual dan praktik kreatif', 'poin' => ['desain' => 3]],
                'd' => ['label' => 'Membaca teori & menghafal konsep', 'poin' => ['pendidikan' => 3]],
            ],
        ],
        7 => [
            'teks' => 'Lingkungan kerja seperti apa yang menjadi impianmu?',
            'pilihan' => [
                'a' => ['label' => 'Laboratorium atau rumah sakit', 'poin' => ['kesehatan' => 3]],
                'b' => ['label' => 'Kantor perusahaan / startup', 'poin' => ['bisnis' => 2, 'teknik' => 1]],
                'c' => ['label' => 'Studio kreatif atau agensi desain', 'poin' => ['desain' => 3]],
                'd' => ['label' => 'Sekolah atau lembaga pendidikan', 'poin' => ['pendidikan' => 3]],
            ],
        ],
        8 => [
            'teks' => 'Objek apa yang paling menarik untuk kamu teliti lebih dalam?',
            'pilihan' => [
                'a' => ['label' => 'Perangkat elektronik atau robot', 'poin' => ['teknik' => 3]],
                'b' => ['label' => 'Tanaman, hewan, atau lingkungan alam', 'poin' => ['pertanian' => 3]],
                'c' => ['label' => 'Perilaku manusia dan masyarakat', 'poin' => ['psikologi' => 2, 'sosial' => 2]],
                'd' => ['label' => 'Angka dan data keuangan', 'poin' => ['bisnis' => 3]],
            ],
        ],
        9 => [
            'teks' => 'Saat menghadapi masalah, kamu cenderung?',
            'pilihan' => [
                'a' => ['label' => 'Mencari solusi teknis secara sistematis', 'poin' => ['teknik' => 2]],
                'b' => ['label' => 'Memahami perasaan pihak yang terlibat', 'poin' => ['psikologi' => 3]],
                'c' => ['label' => 'Menghitung untung-rugi secara rasional', 'poin' => ['bisnis' => 2]],
                'd' => ['label' => 'Mencari dasar hukum/aturan yang berlaku', 'poin' => ['hukum' => 3]],
            ],
        ],
        10 => [
            'teks' => 'Bidang apa yang paling membuatmu penasaran untuk didalami?',
            'pilihan' => [
                'a' => ['label' => 'Sains terapan & teknologi', 'poin' => ['teknik' => 2, 'pertanian' => 1]],
                'b' => ['label' => 'Bahasa asing & budaya dunia', 'poin' => ['bahasa' => 3]],
                'c' => ['label' => 'Psikologi & perilaku manusia', 'poin' => ['psikologi' => 3]],
                'd' => ['label' => 'Dunia pendidikan & cara mengajar', 'poin' => ['pendidikan' => 3]],
            ],
        ],
    ];

    /**
     * Mengambil seluruh daftar pertanyaan (dipakai controller untuk menampilkan form).
     */
    public static function getPertanyaan(): array
    {
        return self::PERTANYAAN;
    }

    /**
     * Method utama inferensi: memproses jawaban user menjadi rekomendasi jurusan.
     *
     * @param array $jawaban  contoh: [1 => 'a', 2 => 'c', 3 => 'b', ...]
     * @return array{skor: array, rekomendasi: array}
     */
    public static function proses(array $jawaban): array
    {
        // 1. Inisialisasi skor semua kategori = 0
        $skor = array_fill_keys(array_keys(self::KATEGORI), 0);

        // 2. Forward chaining: akumulasi poin dari setiap jawaban
        foreach ($jawaban as $idPertanyaan => $pilihanKey) {
            $poinPilihan = self::PERTANYAAN[$idPertanyaan]['pilihan'][$pilihanKey]['poin'] ?? [];

            foreach ($poinPilihan as $kategori => $nilai) {
                if (array_key_exists($kategori, $skor)) {
                    $skor[$kategori] += $nilai;
                }
            }
        }

        // 3. Urutkan skor dari yang tertinggi
        arsort($skor);

        $totalPoin = array_sum($skor) ?: 1; // hindari pembagian dengan nol

        // 4. Ambil 3 kategori teratas sebagai rekomendasi
        $top3 = array_slice($skor, 0, 3, true);

        $rekomendasi = [];
        foreach ($top3 as $kategori => $poin) {
            $info = self::KATEGORI[$kategori];

            $rekomendasi[] = [
                'kategori' => $kategori,
                'nama' => $info['nama'],
                'deskripsi' => $info['deskripsi'],
                'jurusan' => $info['jurusan'],
                'mata_kuliah' => $info['mata_kuliah'],
                'prospek' => $info['prospek'],
                'poin' => $poin,
                'persentase' => round(($poin / $totalPoin) * 100),
            ];
        }

        return [
            'skor' => $skor,
            'rekomendasi' => $rekomendasi,
        ];
    }
}