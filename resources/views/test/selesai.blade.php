@extends('test.main')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg p-3 rounded-4">
                    <div class="mb-4">
                        <!-- Icon Success Check -->
                        <div class="display-1 text-success">🎉</div>
                        <h2 class="fw-bold">Tes Selesai!</h2>
                        <p class="text-muted">Terima kasih telah menyelesaikan ujian ini tepat waktu.</p>
                    </div>

                    <!-- Skor Card -->
                    <div class="bg-light p-4 rounded-4 mb-4">
                        <h6 class="text-uppercase fw-bold text-muted small mb-3">Skor Anda</h6>
                        <div class="display-3 fw-bold text-primary mb-0">{{ $skor }}<span class="fs-4 text-muted">/100</span></div>
                        @if ($skor < 50)
                            <p class="text-muted">Haduh yang bener aja dong. Coba dipikirkan secara logika !!!</p>
                        @elseif ($skor < 75)
                            <p class="text-muted">Lumayan, lebih keras lagi usahanya!</p>
                        @elseif ($skor < 90)
                            <p class="text-muted">Good! Saya suka saya suka !!!.</p>
                        @else
                            <p class="text-muted">Wow, luar biasa! Kamu benar-benar menguasai materi ini dengan sangat baik!</p>
                        @endif
                        
                    </div>

                    <!-- Statistik Singkat -->
                    <div class="row g-3 mb-4">
                        <div class="col-4 border-end">
                            <h5 class="mb-0 fw-bold text-success">{{ $benar }}</h5>
                            <small class="text-muted">Benar</small>
                        </div>
                        <div class="col-4 border-end">
                            <h5 class="mb-0 fw-bold text-danger">{{ $salah }}</h5>
                            <small class="text-muted">Salah</small>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0 fw-bold text-info">{{ $total }} / {{ $soal }}</h5>
                            <small class="text-muted">Terjawab</small>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <button type="button" class="btn btn-danger btn-md shadow-sm" onclick="reset()">Ulangi Test</button>
                        <a href="{{ route('ujian.index') }}" class="btn btn-primary btn-md shadow-sm px-4">Selesai</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function reset(id_siswa = "{{ request()->route('id') }}") {
            fetch('/ujian/reset/' + id_siswa, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    alert(res.message);
                    // 🔥 redirect ke halaman cek peserta
                    window.location.href = "/ujian";
                }
            });
        }
    </script>
@endsection