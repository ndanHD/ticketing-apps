<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kawano Ticketing - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/login.css" rel="stylesheet">
</head>

<body>
    <!-- <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">Kawano Ticketing</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMain">
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
                    @endguest
                </ul>
            </div>
        </div>
    </nav> -->

    <!-- <main class="py-5"> -->
    <div class="container">
        <div class="login-card col-xl-4 col-md-6 col-sm-12 col-12">
            <div class="card shadow-sm">
                <h5 class="my-3">Login</h5>
                <hr class="line-bottom">
                <div class="card-body w-100">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}" class="w-100">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" name="password" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="">
                            <button type="submit" class="btn-login btn btn-success w-100">Login</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- <p class="text-center text-muted mt-3">© {{ date('Y') }} Kawano Ticketing</p> -->
        </div>
    </div>
    <!-- </main> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>