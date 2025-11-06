<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title',  '|Ticketing' )</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { padding-top: 56px; }
        .sidebar { min-height: calc(100vh - 56px); background-color: #f8f9fa; }
        .nav-link { color: #333; }
        .nav-link:hover { color: #0d6efd; }
        .nav-link.active { color: #0d6efd; font-weight: bold; }
    </style>
    @stack('head')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Kawano') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/') }}"><i class="bi bi-house"></i> Home</a></li>

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name ?? auth()->user()->email }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="#">Profil</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout.post') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @php
                        $handlerNew = 0;
                        try {
                            if (auth()->user() && auth()->user()->role && auth()->user()->role->name === 'user_handler') {
                                $handlerNew = \App\Models\Ticket::where('assign_to', auth()->user()->id)
                                    ->where('created_at', '>=', now()->subDay())
                                    ->count();
                            }
                        } catch (\Exception $e) {
                            $handlerNew = 0;
                        }
                    @endphp
                    @if(auth()->user() && auth()->user()->role && auth()->user()->role->name === 'user_handler')
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('handler.tickets.index') }}">
                                <i class="bi bi-bell"></i> Notifikasi
                                @if($handlerNew > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $handlerNew }}</span>
                                @endif
                            </a>
                        </li>
                    @endif
                @endauth

                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login.show') }}"><i class="bi bi-box-arrow-in-right"></i> Login</a></li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0 sidebar">
            <div class="p-3">
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <span class="nav-link text-muted text-uppercase small fw-bold">Manajemen Tiket</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="{{ route('admin.tickets.create') }}">
                            <i class="bi bi-plus-circle me-2"></i> Tambah Tiket
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="{{ route('admin.tickets.index') }}">
                            <i class="bi bi-ticket-perforated me-2"></i> Daftar Tiket
                        </a>
                    </li>
                    @if(auth()->check() && auth()->user()->role && auth()->user()->role->name === 'user_handler')
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="{{ route('handler.tickets.index') }}">
                                <i class="bi bi-person-badge me-2"></i> Tiket Saya (Handler)
                            </a>
                        </li>
                    @endif

                    <li class="nav-item mt-4 mb-2">
                        <span class="nav-link text-muted text-uppercase small fw-bold">Pengaturan</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="{{ route('admin.ticket-types.create') }}">
                            <i class="bi bi-tag me-2"></i> Tambah Tipe Tiket
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="{{ route('admin.ticket-types.index') }}">
                            <i class="bi bi-tags me-2"></i> Daftar Tipe Tiket
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="{{ route('admin.slas.create') }}">
                            <i class="bi bi-clock me-2"></i> Tambah SLA
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="{{ route('admin.slas.index') }}">
                            <i class="bi bi-clock-history me-2"></i> Daftar SLA
                        </a>
                    </li>

                    @if(auth()->check() && (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin')))
                        <li class="nav-item mt-4 mb-2">
                            <span class="nav-link text-muted text-uppercase small fw-bold">Manajemen User</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="{{ route('admin.users.create') }}">
                                <i class="bi bi-person-plus me-2"></i> Tambah User
                            </a>
                        </li>
                        @if(auth()->user()->hasRole('superadmin'))
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center" href="{{ route('admin.users.index') }}">
                                    <i class="bi bi-people me-2"></i> Daftar User
                                </a>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 px-md-4 py-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Pass server-side flash/errors to the client for admin.js to consume
    window.Laravel = <?php echo json_encode([
        'success' => session('success'),
        'error' => session('error'),
        'errors' => $errors->any() ? $errors->all() : []
    ], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/js/admin.js"></script>
</body>
</html>
