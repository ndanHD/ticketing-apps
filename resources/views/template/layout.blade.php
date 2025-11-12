<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Kawano Ticketing')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/homepage.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
            padding-top: 70px;
        }
        .site-nav {
            background: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }
        .site-footer {
            background: #f8f9fa;
            padding: 2rem 0;
            margin-top: 3rem;
        }
    </style>
    @stack('head')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light site-nav fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">{{ config('app.name', 'Kawano') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login.show') }}">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register.show') }}">Register</a></li>
                    @else
                        @php
                            $roleName = auth()->user()->role?->name ?? null;
                            if ($roleName === 'superadmin' || $roleName === 'admin') {
                                $dash = route('admin.dashboard');
                            } elseif (str_contains($roleName ?? '', 'handler')) {
                                $dash = route('handler.dashboard');
                            } else {
                                $dash = route('user.dashboard');
                            }
                        @endphp
                        <li class="nav-item"><a class="nav-link" href="{{ $dash }}">Dashboard</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                <li><a class="dropdown-item" href="#">{{ auth()->user()->name ?? 'Profil' }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout.post') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-5">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="site-footer text-center">
        <div class="container">
            <p class="mb-1">© {{ date('Y') }} {{ config('app.name', 'Kawano Ticketing') }}</p>
            <small class="text-muted">Simple ticketing for teams</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    @stack('scripts')
    <script>
        window.Laravel = <?php echo json_encode(
            [
                'success' => session('success'),
                'error' => session('error'),
                'errors' => $errors->any() ? $errors->all() : [],
            ],
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP,
        ); ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/js/admin.js"></script>
</body>

</html>
