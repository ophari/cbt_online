@extends('test.main')

@section('content')
    <div class="container my-3 py-3 text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-5 rounded-4">
                    <!-- Icon Coffee/Break -->
                    <div class="mb-4 text-warning">
                        <svg xmlns="http://w3.org" width="80" height="80" fill="currentColor" class="bi bi-cup-hot-fill" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M.5 6a.5.5 0 0 0 0 1h15a.5.5 0 0 0 0-1H.5zM.5 9a.5.5 0 0 1 0-1h15a.5.5 0 0 1 0 1H.5zM1.5 10a.5.5 0 0 0 0 1h13a.5.5 0 0 0 0-1h-13z"/>
                        <path d="M3 0c.5 0 1 .5 1 1s-.5 1-1 1-1-.5-1-1 .5-1 1-1zm3 0c.5 0 1 .5 1 1s-.5 1-1 1-1-.5-1-1 .5-1 1-1zm3 0c.5 0 1 .5 1 1s-.5 1-1 1-1-.5-1-1 .5-1 1-1z"/>
                        <path d="M13 11V6h.5a1.5 1.5 0 1 1 0 3H13v2zM12 6H2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V6z"/>
                        </svg>
                    </div>
                    <h2 class="fw-bold mb-3">Waktunya Istirahat Sejenak</h2>
                    <p class="text-muted mb-4">Silakan tarik napas sejenak sebelum melanjutkan ke sesi berikutnya</p>
                    
                    <div class="alert alert-info py-2 small">
                        Soal berikutnya akan dimulai dalam : <strong id="timer">--</strong> Detik
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Coundown timer dinamis
        let countdown = {{ $sisa_jeda ?? 60 }};
        const timerElement = document.getElementById('timer');
        timerElement.textContent = countdown;

        const countDown = setInterval(function() {
            countdown--;
            timerElement.textContent = countdown;

            if (countdown <= 0) {
                window.location.href = "{{ route('ujian.soal', $siswa->id ?? request()->route('id')) }}";
                clearInterval(countDown);
            }
        }, 1000);

    </script>
@endsection