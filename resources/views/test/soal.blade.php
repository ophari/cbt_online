@extends('test.main')

@section('content')
    <!-- Judul & Timer -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="fw-bold mb-0">Ujian Satuan Pendidikan</h3>
            <span class="badge bg-primary">Latihan Soal</span>
        </div>

        <div class="text-end">
            <small class="text-muted d-block">Sisa Waktu</small>
            <h5 id="timer" class="text-danger fw-bold mb-0"></h5>
        </div>
    </div>

    @if(session('error_unanswered'))
        <div class="alert alert-danger alert-dismissible fade show fw-bold shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error_unanswered') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="mb-3 d-flex justify-content-between align-items-center">
        <button class="btn btn-outline-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalNomor">
            <i class="bi bi-grid-3x3-gap-fill me-1"></i> Lihat Nomor Soal
        </button>
        <span class="text-muted fw-bold">Soal Ke-{{ $urutan }} dari {{ count($semua_soal) }}</span>
    </div>
    
    <!-- Modal Daftar Soal -->
    <div class="modal fade" id="modalNomor" tabindex="-1" aria-labelledby="modalNomorLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalNomorLabel">Daftar Soal ({{ ucfirst($tahap) }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($semua_soal as $index => $item)
                            @php
                                $isAnswered = array_key_exists($item->id_soal, $jawaban_user);
                                $btnClass = $isAnswered ? 'btn-success text-white' : 'btn-secondary text-white';
                                if($urutan == $item->urutan) {
                                    $btnClass .= ' border border-3 border-dark';
                                }
                            @endphp
                            <a href="{{ route('ujian.soal', ['id' => $siswa->id]) }}?no={{ $item->urutan }}" class="btn {{ $btnClass }} flex-fill" style="min-width: 45px;">
                                {{ $item->urutan }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Waktu Habis -->
    <div class="modal fade" id="modalWaktuHabis" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="display-1 text-danger mb-3">⏰</div>
                <h4 class="fw-bold text-danger">Waktu Habis!</h4>
                <p class="text-muted">Ujian Anda telah berakhir karena waktu sudah habis. Jawaban yang sudah disimpan akan dihitung.</p>
                <div class="spinner-border text-primary mt-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted small mt-2">Mengalihkan ke halaman hasil...</p>
            </div>
        </div>
    </div>

    <!-- Kartu Soal -->
    <div class="card quiz-card p-4 p-md-5">
        <div class="mb-4">
            <h4 class="mt-2 lh-base">{!! $soal->soal->pertanyaan !!}</h4>
        </div>

        <form id="formJawaban">
            <input type="hidden" id="id_siswa" value="{{ $soal->id_siswa }}">
            <input type="hidden" id="id_soal" value="{{ $soal->id_soal }}">
            <input type="hidden" id="urutan" value="{{ $soal->urutan }}">
            <input type="hidden" id="tahap" value="{{ $soal->tahap }}">
            @php
                // Ambil mapping opsi acak untuk soal ini
                $opsi_map = $soal->opsi_map ?? ['A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E'];
                // opsi_map: posisi tampil => opsi asli
                // Contoh: ['A'=>'C','B'=>'A','C'=>'E','D'=>'B','E'=>'D']
                // Artinya: di posisi A, tampilkan isi jawaban_c

                $jawaban_asli = [
                    'A' => $soal->soal->jawaban_a,
                    'B' => $soal->soal->jawaban_b,
                    'C' => $soal->soal->jawaban_c,
                    'D' => $soal->soal->jawaban_d,
                    'E' => $soal->soal->jawaban_e,
                ];

                // Balik mapping: opsi asli => posisi tampil (untuk menandai jawaban yang sudah dipilih)
                $reverse_map = array_flip($opsi_map);

                $currentAnswer = $jawaban_user[$soal->id_soal] ?? null;
            @endphp

            @foreach (['A','B','C','D','E'] as $posisi_tampil)
                @php
                    $opsi_asli = $opsi_map[$posisi_tampil]; // Opsi asli yang ditampilkan di posisi ini
                    $teks_jawaban = $jawaban_asli[$opsi_asli]; // Isi teks jawaban aslinya
                @endphp
                <div class="option-container">
                    <input type="radio" class="btn-check" name="jawaban" id="opt{{ $loop->index + 1 }}" value="{{ $opsi_asli }}" {{ $currentAnswer == $opsi_asli ? 'checked' : '' }} {{ isset($waktuHabis) && $waktuHabis ? 'disabled' : '' }}>
                    <label class="option-label" for="opt{{ $loop->index + 1 }}">
                        <span class="option-badge">{{ $posisi_tampil }}</span>
                        <span>{!! $teks_jawaban !!}</span>
                    </label>
                </div>
            @endforeach

            <!-- Navigasi -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                @if ($urutan > 1)
                    <a href="{{ route('ujian.soal', ['id' => $siswa->id]) }}?no={{ $urutan - 1 }}" class="btn btn-outline-secondary btn-lg px-4 shadow-sm">
                        <i class="bi bi-arrow-left me-2"></i>Sebelumnya
                    </a>
                @else
                    <div></div>
                @endif
                <button type="button" class="btn btn-primary btn-lg px-5 shadow-sm" onclick="simpanJawaban()">
                    {{ $urutan == count($semua_soal) ? 'Selesai' : 'Simpan & Selanjutnya' }} <i class="bi bi-check2-circle ms-2"></i>
                </button>
            </div>
        </form>
    </div>

    {{-- Anti Copy-Paste CSS --}}
    <style>
        /* Prevent text selection */
        .quiz-card,
        .option-label,
        .option-container,
        h4.lh-base {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>

    <script>
        // Anti Copy-Paste: disable right-click, copy, cut, paste
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        document.addEventListener('copy', function(e) {
            e.preventDefault();
        });
        document.addEventListener('cut', function(e) {
            e.preventDefault();
        });
        document.addEventListener('paste', function(e) {
            e.preventDefault();
        });
        // Block keyboard shortcuts (Ctrl+C, Ctrl+V, Ctrl+U, Ctrl+Shift+I, F12)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && (e.key === 'c' || e.key === 'C' || e.key === 'v' || e.key === 'V' || e.key === 'u' || e.key === 'U' || e.key === 'a' || e.key === 'A')) {
                e.preventDefault();
            }
            if (e.key === 'F12') {
                e.preventDefault();
            }
            if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j')) {
                e.preventDefault();
            }
        });
    </script>

    <script>
        let examFinished = false;

        function simpanJawaban() {
            if (examFinished) return;

            let jawaban = document.querySelector('input[name="jawaban"]:checked');
            if (!jawaban) {
                alert('Pilih jawaban dulu!');
                return;
            }

            fetch('/ujian/simpan_jawaban', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id_siswa: document.getElementById('id_siswa').value,
                    id_soal: document.getElementById('id_soal').value,
                    jawaban: jawaban.value,
                    urutan: document.getElementById('urutan').value,
                    tahap: document.getElementById('tahap').value
                })
            })
            .then(res => res.json())
            .then(res => {
                console.log('RESPONSE:', res);

                if (res.status) {
                    console.log('jawaban berhasil disimpan');
                    let currentNo = parseInt(document.getElementById('urutan').value);
                    let url = new URL(window.location.href);
                    url.searchParams.set('no', currentNo + 1);
                    window.location.href = url.href;
                } else {
                    console.log('gagal:', res.message);
                    // Jika waktu habis dari server, langsung finish
                    if (res.message && res.message.includes('Waktu habis')) {
                        finishExam();
                    } else {
                        alert(res.message);
                    }
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Terjadi kesalahan saat menyimpan jawaban. Silakan coba lagi.');
            });
        }

        function finishExam() {
            if (examFinished) return;
            examFinished = true;

            // Disable semua input
            document.querySelectorAll('input[name="jawaban"]').forEach(el => el.disabled = true);
            document.querySelectorAll('button').forEach(el => el.disabled = true);
            document.querySelectorAll('a.btn').forEach(el => {
                el.style.pointerEvents = 'none';
                el.style.opacity = '0.5';
            });

            // Tampilkan modal waktu habis
            const modal = new bootstrap.Modal(document.getElementById('modalWaktuHabis'));
            modal.show();

            // Kirim request auto-selesai ke server
            fetch('/ujian/auto-selesai', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id_siswa: document.getElementById('id_siswa').value
                })
            })
            .then(res => res.json())
            .then(res => {
                // Redirect ke halaman soal (yang akan menampilkan halaman selesai)
                setTimeout(() => {
                    window.location.href = "/ujian/soal/{{ $siswa->id }}";
                }, 2500);
            })
            .catch(err => {
                console.error('Auto-selesai error:', err);
                // Tetap redirect meskipun error
                setTimeout(() => {
                    window.location.href = "/ujian/soal/{{ $siswa->id }}";
                }, 2500);
            });
        }
    </script>

    <script>
        let sisa = {{ $sisa_waktu }}; // Ini adalah total detik dari PHP
        const display = document.getElementById('timer');

        const x = setInterval(function() {
            if (sisa > 0) {
                sisa--;

                let hours = Math.floor(sisa / 3600);
                let minutes = Math.floor((sisa % 3600) / 60);
                let seconds = sisa % 60;

                let displayHours = hours < 10 ? "0" + hours : hours;
                let displayMinutes = minutes < 10 ? "0" + minutes : minutes;
                let displaySeconds = seconds < 10 ? "0" + seconds : seconds;

                if (hours > 0) {
                    display.innerText = `${displayHours}:${displayMinutes}:${displaySeconds}`;
                } else {
                    display.innerText = `${displayMinutes}:${displaySeconds}`;
                }

                // Warna merah berkedip saat kurang dari 60 detik
                if (sisa <= 60) {
                    display.classList.add('text-danger');
                    display.style.animation = 'blink 1s infinite';
                }

            } else {
                clearInterval(x);
                display.innerText = "Waktu Habis";
                finishExam();
            }
        }, 1000);
    </script>

    <style>
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
    </style>

@endsection
