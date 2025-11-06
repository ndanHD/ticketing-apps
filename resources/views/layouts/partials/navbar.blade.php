<!-- Navigation based on user role -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Ticketing System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            @auth
                @if(auth()->user() && auth()->user()->hasRole('superadmin'))
                    <!-- Superadmin Navigation (includes user management) -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="fas fa-users"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}">
                                <i class="fas fa-user-plus"></i> Add User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}" href="{{ route('admin.tickets.index') }}">
                                <i class="fas fa-ticket-alt"></i> Tickets
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.ticket-types.*') ? 'active' : '' }}" href="{{ route('admin.ticket-types.index') }}">
                                <i class="fas fa-tags"></i> Ticket Types
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.slas.*') ? 'active' : '' }}" href="{{ route('admin.slas.index') }}">
                                <i class="fas fa-clock"></i> SLAs
                            </a>
                        </li>
                    </ul>
                @elseif(auth()->user() && auth()->user()->hasRole('admin'))
                    <!-- Admin Navigation -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}">
                                <i class="fas fa-user-plus"></i> Add User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}" href="{{ route('admin.tickets.index') }}">
                                <i class="fas fa-ticket-alt"></i> Tickets
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.ticket-types.*') ? 'active' : '' }}" href="{{ route('admin.ticket-types.index') }}">
                                <i class="fas fa-tags"></i> Ticket Types
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.slas.*') ? 'active' : '' }}" href="{{ route('admin.slas.index') }}">
                                <i class="fas fa-clock"></i> SLAs
                            </a>
                        </li>
                    </ul>
                @elseif(auth()->user() && auth()->user()->hasRole('handler'))
                    <!-- Handler Navigation -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('handler.dashboard') ? 'active' : '' }}" href="{{ route('handler.dashboard') }}">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('handler.tickets.*') ? 'active' : '' }}" href="{{ route('handler.tickets.index') }}">
                                <i class="fas fa-ticket-alt"></i> Manage Tickets
                            </a>
                        </li>
                    </ul>
                @else
                    <!-- Regular User Navigation -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('user.tickets.*') ? 'active' : '' }}" href="{{ route('user.tickets.index') }}">
                                <i class="fas fa-ticket-alt"></i> My Tickets
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.tickets.create') }}">
                                <i class="fas fa-plus"></i> New Ticket
                            </a>
                        </li>
                    </ul>
                @endif

                <!-- User Profile Dropdown -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('logout.post') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            @else
                <!-- Guest Navigation -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login.show') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register.show') }}">Register</a>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>