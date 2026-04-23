<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Account;
use App\Models\Ujian;
use App\Models\Soal;
use App\Models\Jawaban;
use App\Models\SoalAcak;

use App\Models\LogsActivityUser;

class UserController extends Controller
{
    public function cek_peserta(Request $request)
    {
        // Cek validasi input
        $validate = $request->validate([
            'nisn' => 'required|numeric'
        ]);

        // Siapkan data siswa berdasarkan nomor account
        $siswa = Account::where('nisn', $validate['nisn'])->first();

        // Format tanggal sekarang
        $datetime = Carbon::now()->format('d F Y, H:i') . ' WIB';

        // Jika data siswa tidak ditemukan, kembalikan response error
        if(!$siswa) return back()->withErrors(['nisn' => 'Peserta dengan NISN ' . $validate['nisn'] . ' tidak ditemukan'])->withInput();

        if($siswa->status == 'nonaktif') return back()->withErrors(['staus' => 'Peserta belum aktif'])->withInput();

        // Jika data siswa ditemukan, kembalikan response sukses dengan data siswa
        return view('test.peserta', compact('siswa', 'datetime'));
    }

    public function mulai_ujian($id_siswa)
    {
        // cek kalau sudah pernah ujian dan statusnya selesai → langsung ke halaman selesai
        $cek_ujian = Ujian::where('id_siswa', $id_siswa)->first();
        if ($cek_ujian && $cek_ujian->status == 'selesai') {
            // Hitung jumlah soal
            $soal = Soal::count();

            // Ambil semua jawaban untuk menghitung skor (opsional, bisa disimpan di tabel lain)
            $jawaban = Jawaban::with('soal')
                ->where('id_siswa', $id_siswa)
                ->get();

            $benar = $jawaban->filter(function($j) {
                return $j->jawaban == $j->soal->kunci_jawaban;
            })->count();

            // Total Jawaban
            $total = $jawaban->count();

            // Jawaban salah
            $salah = $total - $benar;

            // Nilai skor (misal 100 jika semua benar, 0 jika semua salah)
            $skor = $total > 0 ? round(($benar / $total) * 100) : 0;
            return view('test.selesai', [
                'soal' => $soal,
                'benar' => $benar,
                'total' => $total,
                'salah' => $salah,
                'skor' => $skor
            ]);
        }

        // Menyimpan data siswa yang akan mengikuti ujian
        $siswa = Account::findOrFail($id_siswa);

        // buat record ujian baru
        $ujian = Ujian::firstOrCreate(
            ['id_siswa' => $id_siswa],
            [
                'nisn' => $siswa->nisn,
                'status' => 'mulai',
                'tahap' => 'umum',
                'mulai_at' => now()
            ]
        );

        // kalau sudah ada tapi belum mulai, update waktu mulai
        if (!$ujian->mulai_at) {
            $ujian->update(['mulai_at' => now()]);
        }

        // Generate soal acak untuk siswa tersebut jika belum ada
        $soal = SoalAcak::with('soal')->where('id_siswa', $id_siswa)->count();
        if($soal == 0) {
            $this->generate_soal($siswa, 'umum');
        }

        // Catat aktivitas mulai ujian
        LogsActivityUser::create([
            'id_siswa' => $siswa->id,
            'activity' => 'mulai_ujian',
            'ip_address' => request()->getClientIp(),
            'user_agent' => request()->userAgent()
        ]);

        // Redirect ke halaman soal
        return redirect()->route('ujian.soal', $id_siswa);
    }

    public function halaman_soal($id_siswa)
    {
        $siswa = Account::findOrFail($id_siswa);
        $ujian = Ujian::firstOrCreate(
            ['id_siswa' => $id_siswa],
            [
                'nisn' => $siswa->nisn,
                'status' => 'mulai',
                'tahap' => 'umum',
                'mulai_at' => now()
            ]
        );

        // Cek jika ujian sudah selesai, langsung tampilkan halaman selesai
        if($ujian->status == 'selesai') {

            $semua_soal = SoalAcak::where('id_siswa', $id_siswa)
                ->where('tahap', $ujian->tahap)
                ->orderBy('urutan')
                ->get();

            $jumlah_jawab = Jawaban::where('id_siswa', $id_siswa)
                ->where('tahap', $ujian->tahap)
                ->count();

            $jawaban_user = Jawaban::where('id_siswa', $id_siswa)
                ->where('tahap', $ujian->tahap)
                ->pluck('id_soal')
                ->toArray();

            // Hitung jumlah soal
            $soal = Soal::count();

            // Ambil semua jawaban untuk menghitung skor (opsional, bisa disimpan di tabel lain)
            $jawaban = Jawaban::with('soal')
                ->where('id_siswa', $id_siswa)
                ->get();

            $benar = $jawaban->filter(function($j) {
                return $j->jawaban == $j->soal->kunci_jawaban;
            })->count();

            // Total Jawaban
            $total = $jawaban->count();

            // Jawaban salah
            $salah = $total - $benar;

            // Nilai skor (misal 100 jika semua benar, 0 jika semua salah)
            $skor = $total > 0 ? round(($benar / $total) * 100) : 0;

            return view('test.selesai', [
                'soal' => $soal,
                'benar' => $benar,
                'total' => $total,
                'salah' => $salah,
                'skor' => $skor,
                'semua_soal' => $semua_soal,
            ]);
        }


        // Jika waktu sudah habis maka update ke status ujian selesai
        $waktu_mulai = $ujian->mulai_at;
        $durasi = 60 * 60; // 60 menit
        $sisa_waktu = (int) max(0, $durasi - now()->diffInSeconds($waktu_mulai));

        if ($sisa_waktu <= 0) {
            $ujian->update([
                'status' => 'selesai',
                'selesai_at' => now()
            ]);

            // Hitung jumlah soal
            $soal = Soal::count();

            // Ambil semua jawaban untuk menghitung skor (opsional, bisa disimpan di tabel lain)
            $jawaban = Jawaban::with('soal')
                ->where('id_siswa', $id_siswa)
                ->get();

            $benar = $jawaban->filter(function($j) {
                return $j->jawaban == $j->soal->kunci_jawaban;
            })->count();

            // Total Jawaban
            $total = $jawaban->count();

            // Jawaban salah
            $salah = $total - $benar;

            // Nilai skor (misal 100 jika semua benar, 0 jika semua salah)
            $skor = $total > 0 ? round(($benar / $total) * 100) : 0;

            $semua_soal = SoalAcak::where('id_siswa', $id_siswa)
                ->where('tahap', $ujian->tahap)
                ->orderBy('urutan')
                ->get();

            return view('test.selesai', [
                'soal' => $soal,
                'benar' => $benar,
                'total' => $total,
                'salah' => $salah,
                'skor' => $skor,
                'semua_soal' => $semua_soal
            ]);
        }

        // Hitung sisa waktu
        $mulai_at = \Carbon\Carbon::parse($ujian->mulai_at); // Contoh: 2026-04-12 16:00:00
        $durasi_menit = 60;
        $waktu_selesai = $mulai_at->copy()->addMinutes($durasi_menit);

        // Hitung selisih detik antara SEKARANG dan WAKTU SELESAI
        // diffInSeconds dengan parameter 'false' agar hasilnya negatif jika sudah lewat
        $sekarang = now();
        $sisa_detik = $sekarang->diffInSeconds($waktu_selesai, false);

        // Jika sisa_detik negatif, artinya waktu sudah habis, maka jadikan 0
        $hasil_akhir = (int) max(0, $sisa_detik);

        // Jalankan pengecekan tahap (Umum ke Jeda)
        $this->cek_tahap($siswa, $ujian);

        // Handel halaman jeda
        // Jika Tahap jeda maka tampilkan halaman jeda
        if($ujian->tahap == 'jeda') {
            // Cek waktu selesai tahap umum
            // Jika tidak ada waktu selesai tahap umum, tampilkan halaman jeda
            if(!$ujian->waktu_selesai_umum) {
                return view('test.jeda');
            }

            // Variabel untuk menyimpan waktu selesai tahap umum
            $selesai_umum = Carbon::parse($ujian->waktu_selesai_umum);
            $sudah_berlalu = (int) $selesai_umum->diffInSeconds(now());
            $sisa_jeda = 60 - $sudah_berlalu;
            $sisa_jeda = (int) max(0, $sisa_jeda);

            // Cek status ujian jika selesai maka tampilkan halaman selesai atau tampilkan halaman jeda jika belum lewat 60 detik
            if($sisa_jeda > 0) {
                // cek status jika sudah selesai maka tampilkan halaman selesai
                $status = $ujian->status;
                if($ujian->status == 'selesai') {
                    // Hitung jumlah soal
                    $soal = Soal::count();

                    // Ambil semua jawaban untuk menghitung skor (opsional, bisa disimpan di tabel lain)
                    $jawaban = Jawaban::with('soal')
                        ->where('id_siswa', $id_siswa)
                        ->get();

                    $benar = $jawaban->filter(function($j) {
                        return $j->jawaban == $j->soal->kunci_jawaban;
                    })->count();

                    // Total Jawaban
                    $total = $jawaban->count();

                    // Jawaban salah
                    $salah = $total - $benar;

                    // Nilai skor (misal 100 jika semua benar, 0 jika semua salah)
                    $skor = $total > 0 ? round(($benar / $total) * 100) : 0;

                    $semua_soal = SoalAcak::where('id_siswa', $id_siswa)
                        ->where('tahap', $ujian->tahap)
                        ->orderBy('urutan')
                        ->get();

                    return view('test.selesai', [
                        'soal' => $soal,
                        'benar' => $benar,
                        'total' => $total,
                        'salah' => $salah,
                        'skor' => $skor,
                        'semua_soal' => $semua_soal
                    ]);
                }

                // Jika tidak tampilkan halaman jeda dengan waktu sisa
                return view('test.jeda', [
                    'sisa_jeda' => $sisa_jeda,
                    'siswa' => $siswa
                ]);
            }

            // jika sudah lewat 60 detik maka lanjut ke tahap kejuruan
            // Update tahap ke kejuruan
            $ujian->update(['tahap' => 'kejuruan']);

            // Generat soal kejuruan jika belum ada
            if(SoalAcak::with('soal')
                ->where('id_siswa', $siswa->id)
                ->where('tahap', 'kejuruan')
                ->count() == 0) {
                $this->generate_soal($siswa, 'kejuruan');
            }

            return redirect()->route('ujian.soal', $id_siswa);
        }

        $no = request()->query('no');
        if ($no) {
            $soal_acak = SoalAcak::with('soal')
                ->where('id_siswa', $id_siswa)
                ->where('tahap', $ujian->tahap)
                ->where('urutan', $no)
                ->first();
        } else {
            // Soal berikutnya berdasarkan jumlah jawaban yang sudah dijawab
            $jumlah_jawab = Jawaban::where('id_siswa', $id_siswa)
                ->where('tahap', $ujian->tahap)
                ->count();

            $soal_acak = SoalAcak::with('soal')
                ->where('id_siswa', $id_siswa)
                ->where('tahap', $ujian->tahap)
                ->orderBy('urutan')
                ->skip($jumlah_jawab)
                ->first();
        }

        // Jika soal habis (atau nomor di luar batas), cek tahap dan update status
        if(!$soal_acak) {
            // Cek apakah ada soal yang belum dijawab di tahap ini
            $semua_soal_tahap_ini = SoalAcak::where('id_siswa', $id_siswa)
                ->where('tahap', $ujian->tahap)
                ->pluck('id_soal')
                ->toArray();
            
            $jawaban_tahap_ini = Jawaban::where('id_siswa', $id_siswa)
                ->where('tahap', $ujian->tahap)
                ->pluck('id_soal')
                ->toArray();
            
            $soal_belum_dijawab = array_diff($semua_soal_tahap_ini, $jawaban_tahap_ini);
            
            if (count($soal_belum_dijawab) > 0) {
                $id_belum_pertama = reset($soal_belum_dijawab);
                
                $semua_urutan_belum = SoalAcak::where('id_siswa', $id_siswa)
                    ->where('tahap', $ujian->tahap)
                    ->whereIn('id_soal', $soal_belum_dijawab)
                    ->orderBy('urutan')
                    ->pluck('urutan')
                    ->toArray();
                
                $urutan_pertama = SoalAcak::where('id_siswa', $id_siswa)
                    ->where('tahap', $ujian->tahap)
                    ->where('id_soal', $id_belum_pertama)
                    ->value('urutan');

                return redirect()->route('ujian.soal', ['id' => $id_siswa, 'no' => $urutan_pertama])
                    ->with('error_unanswered', 'Anda tidak bisa menyelesaikan tahap ini karena belum menjawab soal nomor: ' . implode(', ', $semua_urutan_belum) . '.');
            }

            // Selesai tahap umum → ke jeda
            if($ujian->tahap == 'umum') {
                return view('test.jeda', ['waktu_selesai_umum' => $ujian->waktu_selesai_umum]);
            }

            // Selesai tahap kejuruan → selesai ujian
            if($ujian->tahap == 'kejuruan') {
                $ujian->update([
                    'status' => 'selesai',
                    'selesai_at' => now()
                ]);

                // Hitung jumlah soal
                $soal = Soal::count();

                // Ambil semua jawaban
                $jawaban = Jawaban::with('soal')
                    ->where('id_siswa', $id_siswa)
                    ->get();

                $benar = $jawaban->filter(function($j) {
                    return $j->jawaban == $j->soal->kunci_jawaban;
                })->count();

                // Total Jawaban
                $total = $jawaban->count();

                // Jawaban salah
                $salah = $total - $benar;

                // Nilai skor (misal 100 jika semua benar, 0 jika semua salah)
                $skor = $total > 0 ? round(($benar / $total) * 100) : 0;

                $semua_soal = SoalAcak::where('id_siswa', $id_siswa)
                    ->where('tahap', $ujian->tahap)
                    ->orderBy('urutan')
                    ->get();

                return view('test.selesai', [
                    'soal' => $soal,
                    'benar' => $benar,
                    'total' => $total,
                    'salah' => $salah,
                    'skor' => $skor,
                    'semua_soal' => $semua_soal
                ]);
            }
        }

        $semua_soal = SoalAcak::where('id_siswa', $id_siswa)
            ->where('tahap', $ujian->tahap)
            ->orderBy('urutan')
            ->get();

        $jawaban_user = Jawaban::where('id_siswa', $id_siswa)
            ->where('tahap', $ujian->tahap)
            ->pluck('jawaban', 'id_soal')
            ->toArray();

        return view('test.soal', [
            'siswa' => $siswa,
            'soal' => $soal_acak,
            'urutan' => $soal_acak->urutan,
            'tahap' => $ujian->tahap,
            'sisa_waktu' => $hasil_akhir,
            'semua_soal' => $semua_soal,
            'jawaban_user' => $jawaban_user
        ]);
    }

    private function cek_tahap($siswa, $ujian)
    {
        // Hitung sisa waktu ujian (Umum)
        $mulai_at = \Carbon\Carbon::parse($ujian->mulai_at);
        $durasi_menit = 60; // Durasi total ujian umum
        $waktu_selesai = $mulai_at->copy()->addMinutes($durasi_menit);
        $is_time_up = now()->greaterThanOrEqualTo($waktu_selesai);

        // cek kalau sudah selesai tahap umum atau waktu habis → update ke jeda
        if ($ujian->tahap == 'umum') {
            $total_soal_umum = SoalAcak::where('id_siswa', $siswa->id)
                ->where('tahap', 'umum')
                ->count();

            $jumlah_jawab = Jawaban::where('id_siswa', $siswa->id)
                ->where('tahap', 'umum')
                ->count();

            // Selesai jika semua soal dijawab ATAU waktu habis
            if (($jumlah_jawab == $total_soal_umum && $total_soal_umum > 0) || $is_time_up) {
                // Cek apakah ada soal untuk tahap selanjutnya (kejuruan)
                $kategori_kejuruan = $this->get_kategori_soal($siswa, 'kejuruan');
                $jumlah_soal_kejuruan = Soal::whereIn('kategori', (array) $kategori_kejuruan)->count();

                // Jika tidak ada soal kejuruan (berarti umum adalah tahap terakhir), langsung jadikan kejuruan tanpa jeda
                if ($jumlah_soal_kejuruan == 0) {
                    $ujian->update([
                        'tahap' => 'kejuruan'
                    ]);
                    return;
                }

                // update tahap ke jeda dan simpan waktu selesai umum (untuk hitung mundur jeda)
                $ujian->update([
                    'tahap' => 'jeda',
                    'waktu_selesai_umum' => now()
                ]);

                return;
            }
        }

        if($ujian->tahap == 'jeda') {
            if (!$ujian->waktu_selesai_umum) {
                return;
            }

            $selesai_umum = Carbon::parse($ujian->waktu_selesai_umum);
            $sudah_berlalu = (int) $selesai_umum->diffInSeconds(now());

            // Jika belum lewat 60 detik, biarkan di halaman jeda
            if($sudah_berlalu < 60) {
                return;
            }

            // ✅ sudah lewat 60 detik → lanjut ke kejuruan
            $ujian->update(['tahap' => 'kejuruan']);
            // Generate soal kejuruan jika belum ada
            if(SoalAcak::where('id_siswa', $siswa->id)
                ->where('tahap', 'kejuruan')
                ->count() == 0) {
                $this->generate_soal($siswa, 'kejuruan');
            }

            logger()->info('Lanjut ke tahap kejuruan', [
                'id_siswa' => $siswa->id,
                'waktu_selesai_umum' => $ujian->waktu_selesai_umum
            ]);
        }
    }

    private function generate_soal($siswa, $tahap)
    {
        $kategori = $this->get_kategori_soal($siswa, $tahap);

        $soal = Soal::whereIn('kategori', (array) $kategori)
            ->inRandomOrder()
            ->get();

        foreach ($soal as $index => $s) {
            SoalAcak::create([
                'id_siswa' => $siswa->id,
                'id_soal' => $s->id,
                'tahap' => $tahap,
                'urutan' => $index + 1
            ]);
        }
    }

    private function get_kategori_soal($siswa, $tahap)
    {
        $jurusan = strtoupper($siswa->jurusan);
        if($tahap == 'umum') {
            return ['umum'];
        }

        if($tahap == 'kejuruan') {
            if ($jurusan == 'RPL') {
                return ['rpl'];
            }
        }

        return [];
    }

    public function simpan_jawaban(Request $request)
    {
        $ujian = Ujian::where('id_siswa', $request->id_siswa)->first();
        if(!$ujian || !$ujian->mulai_at) {
            return response()->json([
                'status' => false,
                'message' => 'Ujian tidak valid'
            ], 400);
        }

        $durasi = 60 * 60; // 60 menit
        $sisa_waktu = (int) max(0, $durasi - now()->diffInSeconds($ujian->mulai_at));

        // Blok kalau waktu sudah habis, tapi masih ada request untuk menyimpan jawaban
        if($sisa_waktu <= 0 || $ujian->status == 'selesai') {
            // Update status ujian ke selesai jika belum selesai
            if($ujian->status != 'selesai') {
                $ujian->update([
                    'status' => 'selesai',
                    'selesai_at' => now()
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Waktu habis, ujian sudah selesai'
            ], 400);
        }

        // Jika waktu habis, tidak bisa menyimpan jawaban
        if ($sisa_waktu <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Waktu habis, tidak bisa menyimpan jawaban dari sisa pertanyaan ini'
            ], 400);
        }

        logger()->info('Menyimpan jawaban', [
            'id_siswa' => $request->id_siswa,
            'id_soal' => $request->id_soal,
            'jawaban' => $request->jawaban,
            'urutan' => $request->urutan,
            'tahap' => $ujian->tahap
        ]);

        $jawaban = Jawaban::updateOrCreate(
            [
                'id_siswa' => $request->id_siswa,
                'id_soal' => $request->id_soal
            ],
            [
                'jawaban' => $request->jawaban,
                'urutan' => $request->urutan,
                'tahap' => $ujian->tahap
            ]
        );

        return response()->json([
            'status' => true
        ]);
    }

    public function reset_ujian($id_siswa)
    {
        // Hapus semua data ujian, soal acak, dan jawaban untuk siswa tersebut
        Ujian::where('id_siswa', $id_siswa)->delete();
        SoalAcak::with('soal')->where('id_siswa', $id_siswa)->delete();
        Jawaban::where('id_siswa', $id_siswa)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Ujian berhasil direset'
        ]);
    }
}
