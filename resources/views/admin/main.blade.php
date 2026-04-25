<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard - Bootstrap 5.3</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        <style>
            :root {
                --sidebar-bg: #1e293b; /* Slate 800 */
                --sidebar-hover: #334155;
                --accent-color: #3b82f6; /* Blue 500 */
                --sidebar-width: 15rem;
            }

            body {
                overflow-x: hidden;
                background-color: #f1f5f9;
                font-family: 'Inter', sans-serif;
            }

            .container-fluid {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }

            #wrapper {
                display: flex;
                min-height: 100vh;
                position: relative;
                width: 100%;
                align-items: stretch;
            }

            #sidebar-wrapper {
                position: fixed;
                width: var(--sidebar-width);
                margin-left: -15rem;
                left: 0;
                top: 32px; /* Height of the navbar */
                height: calc(100vh - 32px);
                overflow-y: auto;
                scrollbar-width: none; /* Firefox */
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                background-color: var(--sidebar-bg);
                box-shadow: 4px 0 10px rgba(0,0,0,0.1);
                z-index: 1000;
            }

            #sidebar-wrapper::-webkit-scrollbar {
                display: 4px;
            }

            #sidebar-wrapper::-webkit-scrollbar-thumb {
                background: rgba(255,255,255,0.2);
                border-radius: 10px;
            }

            /* Logo / Branding */
            .sidebar-heading {
                padding: 2rem 1.5rem;
                font-size: 1.4rem;
                color: white;
                letter-spacing: 1px;
                display: flex;
                align-items: center;
            }

            .sidebar-heading i {
                color: var(--accent-color);
                font-size: 1.8rem;
            }

            /* Menu Styling */
            .list-group-item {
                border: none !important;
                padding: 0.8rem 1.5rem !important;
                margin: 0.2rem 0.8rem;
                border-radius: 8px !important;
                color: #94a3b8 !important;
                transition: all 0.2s;
                display: flex;
                align-items: center;
            }

            .list-group-item i {
                font-size: 1.2rem;
                width: 25px;
                margin-right: 10px;
            }

            .list-group-item:hover {
                background-color: var(--sidebar-hover) !important;
                color: white !important;
            }

            .list-group-item.active {
                background-color: var(--accent-color) !important;
                color: white !important;
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            }

            /* Label Menu (Optional) */
            .menu-label {
                color: #475569;
                font-size: 0.75rem;
                text-transform: uppercase;
                font-weight: 700;
                margin: 1.5rem 1.5rem 0.5rem;
            }

            #page-content-wrapper {
                display: flex;
                flex-direction: column;
                width: 100%;
                padding-top: 32px;
                transition: all 0.3s ease;
            }

            body.sb-sidenav-toggled #sidebar-wrapper {
                margin-left: 0;
            }

            footer {
                margin-top: auto;
            }

            @media (min-width: 768px) {
                #sidebar-wrapper {
                    margin-left: 0;
                }
                #page-content-wrapper {
                    padding-left: var(--sidebar-width);
                    /* min-width: 0;
                    width: 100%; */
                }
                body.sb-sidenav-toggled #sidebar-wrapper {
                    margin-left: -15rem;
                }

                body.sb-sidenav-toggled #page-content-wrapper {
                    padding-left: 0;
                }
            }

            @media (max-width: 767.98px) {

                #dataTable td {
                    font-size: 10px;
                }

                #dataTable th {
                    font-size: 12px;
                }

                .btn {
                    font-size: 10px;
                    padding: 0.25rem 0.5rem;
                }

                .modal-title {
                    font-size: 14px;
                }

                #sidebar-wrapper {
                    margin-left: 0;
                }
                #page-content-wrapper {
                    padding-left: 0;
                    min-width: 0;
                    width: 100%;
                }
                body.sb-sidenav-toggled #sidebar-wrapper {
                    margin-left: -15rem;
                }

                body.sb-sidenav-toggled #page-content-wrapper {
                    margin-left: 0;
                }
            }
        </style>
    </head>
    <body>

    <div class="d-flex min-vh-100" id="wrapper">
        <div id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom border-secondary border-opacity-25">
                <i class="bi bi-cpu-fill me-2"></i> <strong>CBT</strong> Online
            </div>

            <div class="list-group list-group-flush mt-3">
                <div class="menu-label">Menu Utama</div>
                <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.index') active @endif" href="{{ route('admin.index') }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
                <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.peserta') active @endif" href="{{ route('admin.peserta') }}">
                    <i class="bi bi-people-fill"></i> Peserta
                </a>
                <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.soal') active @endif" href="{{ route('admin.soal') }}">
                    <i class="bi bi-question-circle-fill"></i> Soal
                </a>

                <div class="menu-label">Pelaksanaan</div>
                <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.token') active @endif" href="{{ route('admin.token') }}">
                    <i class="bi bi-key-fill"></i> Token Ujian
                </a>
                <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.aktif_peserta') active @endif" href="{{ route('admin.aktif_peserta') }}">
                    <i class="bi bi-people-fill"></i> Aktif Peserta
                </a>
                <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.reset_peserta') active @endif" href="{{ route('admin.reset_peserta') }}">
                    <i class="bi bi-arrow-repeat"></i> Reset Peserta
                </a>
                <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.koreksi') active @endif" href="{{ route('admin.koreksi') }}">
                    <i class="bi bi-pencil-square"></i> Koreksi
                </a>

                <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.riwayat') active @endif" href="{{ route('admin.riwayat') }}">
                    <i class="bi bi-clock-history"></i> Riwayat
                </a>

                <div class="menu-label">Sistem</div>
                <a class="list-group-item list-group-item-action bg-transparent " href="#!">
                    <i class="bi bi-cloud-arrow-down-fill"></i> Backup
                </a>
                <a class="list-group-item list-group-item-action bg-transparent text-danger-emphasis" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-left"></i> Keluar
                </a>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm fixed-top">
                <div class="container-fluid">
                    <button class="btn btn-outline-dark btn-sm" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center">
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                                <img src="https://ui-avatars.com/api/?name=Admin" alt="mdo" width="32" height="32" class="rounded-circle me-2">
                                <strong>Admin</strong>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="#">Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Keluar</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                @yield('content')
            </div>

            <footer class="bg-white border-top py-3 mt-auto shadow-sm ">
                <div class="container-fluid">
                    <p class="mb-0 text-center text-muted">&copy; 2023 Your Company. All rights reserved.</p>
                </div>
            </footer>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk Toggle Sidebar
        window.addEventListener('DOMContentLoaded', event => {
            const sidebarToggle = document.body.querySelector('#sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                    localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
                });
            }
        });
    </script>
    </body>
</html>
