<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\SoalImport;
use App\Models\Account;
use App\Models\Jawaban;
use App\Models\LogsActivityUser;
use App\Models\Soal;
use App\Models\SoalAcak;
use App\Models\TokenUjian;
use App\Models\Ujian;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function index()
    {
        $peserta = Account::count();
        $laki_laki = Account::where('jenis_kelamin', 'Laki-Laki')->count();
        $perempuan = Account::where('jenis_kelamin', 'Perempuan')->count();

        $log = LogsActivityUser::orderBy('created_at', 'desc')->get();

        return view('admin.pages.beranda', compact('peserta', 'laki_laki', 'perempuan', 'log'));
    }

    public function peserta()
    {
        $peserta = Account::all();

        return view('admin.pages.peserta', compact('peserta'));
    }

    public function soal()
    {
        $soal = Soal::all();

        return view('admin.pages.soal', compact('soal'));
    }

    public function peserta_aktif()
    {
        $peserta_aktif = Account::all();

        return view('admin.pages.aktif_peserta', compact('peserta_aktif'));
    }

    public function reset_peserta()
    {
        $pesertas = Ujian::with('account')->get();
        foreach ($pesertas as $ujian) {
            if ($ujian->status != 'selesai' && $ujian->mulai_at) {
                $durasi = 60 * 60; // 60 menit
                $sisa_waktu = (int) max(0, $durasi - now()->diffInSeconds($ujian->mulai_at));
                if ($sisa_waktu <= 0) {
                    $ujian->update([
                        'status' => 'selesai',
                        'selesai_at' => now(),
                    ]);
                }
            }
        }
        $peserta = Ujian::with('account')->get();

        return view('admin.pages.reset', compact('peserta'));
    }

    // Koreksi jawaban peserta
    public function koreksi()
    {
        // $ujian = Ujian::findOrFail($id);
        $jawaban = Jawaban::with(['account', 'soal'])
            ->orderBy('id_siswa', 'asc')
            ->get();

        $details = Jawaban::with(['account', 'soal'])
            ->get()
            ->map(function ($item) {
                return [
                    'id_siswa' => $item->id_siswa,
                    'name' => $item->account->name,
                    'pertanyaan' => $item->soal->pertanyaan,
                    'jawaban' => $item->jawaban,
                    'kunci_jawaban' => $item->soal->kunci_jawaban,
                ];
            });

        $detail_jawaban = Jawaban::with(['soal', 'account'])
            ->get()
            ->groupBy('id_siswa')
            ->map(function ($items) {
                $benar = 0;
                $salah = 0;
                foreach ($items as $item) {
                    if ($item->jawaban === $item->soal->kunci_jawaban) {
                        $benar++;
                    } else {
                        $salah++;
                    }
                }

                // TOTAL SOAL (bukan jumlah jawaban!)
                $jumlah_soal = Soal::count();

                $jumlah_jawaban = Jawaban::where('id_siswa', $items[0]->id_siswa)->count();

                // Jumlah soal yang di jawab
                $jumlah_soal_acak = SoalAcak::where('id_siswa', $items[0]->id_siswa)->count();

                $soal_tidak_dijawab = $jumlah_soal - $jumlah_soal_acak;
                $nilai = $jumlah_soal > 0
                    ? round(($benar / $jumlah_soal) * 100, 2)
                    : 0;

                return [
                    'id_siswa' => $items[0]->id_siswa,
                    'name' => $items[0]->account->name,
                    'jumlah_soal' => $jumlah_soal,
                    'benar' => $benar,
                    'salah' => $salah,
                    'soal_tidak_dijawab' => $soal_tidak_dijawab,
                    'nilai' => $nilai,
                    'detail' => $items->map(function ($item) {
                        return [
                            'pertanyaan' => $item->soal->pertanyaan,
                            'jawaban' => $item->jawaban,
                            'kunci_jawaban' => $item->soal->kunci_jawaban,
                        ];
                    }),
                ];
            });

        return view('admin.pages.koreksi', compact('detail_jawaban', 'jawaban', 'details'));
    }

    // Fungsi untuk menampilkan riwayat ujian peserta
    public function riwayat()
    {
        $riwayat = Ujian::with('account')->get();

        return view('admin.pages.riwayat', compact('riwayat'));
    }

    public function aktifkan_seluruh_peserta()
    {
        try {
            Account::where('status', '!=', 'aktif')->update([
                'status' => 'aktif',
            ]);

            return back()->with('success', 'Semua peserta sudah aktif');
        } catch (\Exception $e) {
            return back()->with('failed', 'Gagal mengaktifkan peserta. '.$e->getMessage());
        }
    }

    public function nonaktifkan_seluruh_peserta()
    {
        try {
            Account::where('status', '!=', 'nonaktif')->update([
                'status' => 'nonaktif',
            ]);

            return back()->with('success', 'Semua peserta sudah di nonaktifkan');
        } catch (\Exception $e) {
            return back()->with('failed', 'Gagal menonaktifkan peserta. '.$e->getMessage());
        }
    }

    public function nonaktifkan_peserta($id)
    {
        try {
            $peserta = Account::findOrFail($id);

            if ($peserta->status === 'nonaktif') {
                return;
            }

            $peserta->update([
                'status' => 'nonaktif',
            ]);

            return redirect()->route('admin.aktif_peserta');
        } catch (\Exception $e) {
            return back()->with('failed', 'Gagal menonaktifkan peserta. '.$e->getMessage());
        }
    }

    public function aktifkan_peserta($id)
    {
        try {
            $peserta = Account::findOrFail($id);

            if ($peserta->status === 'aktif') {
                return;
            }

            $peserta->update([
                'status' => 'aktif',
            ]);

            return redirect()->route('admin.aktif_peserta');
        } catch (\Exception $e) {
            return back()->with('failed', 'Gagal aktifkan peserta. '.$e->getMessage());
        }
    }

    // Fungsi untuk upload soal dari file Excel
    public function importSoal(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv',
            ]);

            $file = $request->file('file');
            $import = new SoalImport;
            Excel::import($import, $file);

            return redirect()->back()->with('success', 'Soal berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Soal gagal diimpor! : '.$e->getMessage());
        }

    }

    public function reset($id_siswa)
    {
        try {
            Ujian::where('id_siswa', $id_siswa)->delete();
            SoalAcak::where('id_siswa', $id_siswa)->delete();
            Jawaban::where('id_siswa', $id_siswa)->delete();

            return back()->with('success', 'Berhasil reset ujian');
        } catch (\Throwable $e) {
            return back()->with('failed', 'Gagal reset ujian : '.$e->getMessage());
        }
    }

    // ==========================================
    // TOKEN UJIAN BERKALA
    // ==========================================

    public function token_ujian()
    {
        $token = TokenUjian::getActiveToken();

        return view('admin.pages.token', compact('token'));
    }

    public function generate_token()
    {
        $token = TokenUjian::generateToken();

        return back()->with('success', 'Token baru berhasil di-generate: '.$token->token);
    }

    public function get_active_token()
    {
        $token = TokenUjian::getActiveToken();

        return response()->json([
            'token' => $token ? $token->token : null,
            'expired_at' => $token && $token->expired_at ? $token->expired_at->toIso8601String() : null,
            'is_active' => $token ? true : false,
        ]);
    }
}
