<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kawano Ticketing</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light site-nav">
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
</nav>

<header class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="display-5 fw-bold">Support faster, smarter and kinder customer experiences</h1>
                <p class="lead mb-4">Kawano Ticketing helps teams manage requests, measure SLAs and collect feedback — all in one simple interface.</p>
                <div class="d-flex gap-2 align-items-center mb-4">
                    <a href="{{ route('register.show') }}" class="btn btn-primary btn-lg">Get Started</a>
                    <a href="{{ route('login.show') }}" class="btn btn-outline-primary btn-lg">Sign In</a>
                    <span class="badge badge-soft ms-3">No credit card required</span>
                </div>
                <div class="hero-card d-inline-block">
                    <div class="row g-2">
                        <div class="col-6 border-end">
                            <div class="small text-muted">Avg. resolution</div>
                            <div class="fw-bold">2.3 days</div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted">Rating (past 30d)</div>
                            <div class="fw-bold">4.7 / 5</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-center mt-4 mt-lg-0">
                <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=800&q=60" class="img-fluid rounded" alt="dashboard screenshot" style="box-shadow:0 20px 40px rgba(91,33,182,0.12)">
            </div>
        </div>
    </div>
</header>

<main class="py-5">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="feature-card h-100 text-center">
                    <div class="feature-icon mb-3">📨</div>
                    <h5>Unified Inbox</h5>
                    <p class="text-muted">All incoming requests captured in a single queue. Tag, prioritize and assign with ease.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card h-100 text-center">
                    <div class="feature-icon mb-3">🧭</div>
                    <h5>Intelligent Routing</h5>
                    <p class="text-muted">Automatically route tickets to the right handler based on SLA and skill.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card h-100 text-center">
                    <div class="feature-icon mb-3">📊</div>
                    <h5>Insights & Ratings</h5>
                    <p class="text-muted">Track performance with built-in metrics and collect customer feedback per ticket.</p>
                </div>
            </div>
        </div>

        <div class="row align-items-center mb-5">
            <div class="col-lg-6">
                <h3 class="mb-3">Designed for teams of all sizes</h3>
                <p class="text-muted">From small support teams to enterprise ops, Kawano Ticketing scales with your needs. Configure roles, SLAs, and workflows without complexity.</p>
                <ul class="text-muted">
                    <li>Fine-grained roles & permissions</li>
                    <li>Flexible SLA definitions and alerts</li>
                    <li>Exportable reports and audit trails</li>
                </ul>
                <a class="btn btn-outline-primary mt-3" href="{{ route('login.show') }}">Try demo</a>
            </div>
            <div class="col-lg-6 text-center">
                <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=800&q=60" class="img-fluid rounded" alt="features">
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="cta d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Ready to improve your support?</h4>
                        <p class="mb-0">Start a free account and bring clarity to your support operations.</p>
                    </div>
                    <div>
                        <a href="{{ route('register.show') }}" class="btn btn-light btn-lg">Create account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="bg-light py-4 mt-5">
    <div class="container text-center">
        <p class="mb-1">© {{ date('Y') }} Kawano Ticketing</p>
        <small class="text-muted">Made with Bootstrap · Simple ticketing for teams</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>