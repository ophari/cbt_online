@extends('admin.main')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h2 class="mb-4">Token Ujian Berkala</h2>

    <div class="row g-4">
        <!-- Panel Token Aktif -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-5">
                    <p class="text-muted mb-2">Token Aktif Saat Ini</p>
                    @if($token)
                        <h1 class="display-3 fw-bold text-primary mb-3" id="tokenDisplay" style="letter-spacing: 10px;">{{ $token->token }}</h1>
                        <div class="mb-3">
                            <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i> Aktif</span>
                        </div>
                        <p class="text-muted mb-0">Kadaluarsa dalam: <strong id="tokenCountdown" class="text-danger">--:--</strong></p>
                    @else
                        <h1 class="display-3 fw-bold text-secondary mb-3" style="letter-spacing: 10px;">-----</h1>
                        <div class="mb-3">
                            <span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i> Tidak Ada Token Aktif</span>
                        </div>
                        <p class="text-muted mb-0">Klik tombol di bawah untuk membuat token baru.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panel Kontrol -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 fw-bold text-primary"><i class="bi bi-gear me-2"></i>Kontrol Token</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted">Token akan otomatis kadaluarsa setiap <strong>15 menit</strong>. Guru pengawas cukup menuliskan token di papan tulis kelas.</p>
                    
                    <form action="{{ route('admin.token.generate') }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">
                            <i class="bi bi-arrow-repeat me-2"></i> Generate Token Baru
                        </button>
                    </form>

                    <div class="alert alert-info py-2 small mb-0">
                        <i class="bi bi-info-circle me-1"></i> Membuat token baru akan otomatis menonaktifkan token sebelumnya.
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($token && $token->expired_at)
    <script>
        // Countdown timer untuk token
        let tokenExpiry = new Date("{{ $token->expired_at->toIso8601String() }}").getTime();

        const tokenTimer = setInterval(function() {
            let now = new Date().getTime();
            let distance = tokenExpiry - now;

            if (distance <= 0) {
                clearInterval(tokenTimer);
                document.getElementById('tokenCountdown').innerText = 'KADALUARSA';
                document.getElementById('tokenDisplay').classList.remove('text-primary');
                document.getElementById('tokenDisplay').classList.add('text-secondary');
                return;
            }

            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);
            document.getElementById('tokenCountdown').innerText =
                (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        }, 1000);
    </script>
    @endif
@endsection
