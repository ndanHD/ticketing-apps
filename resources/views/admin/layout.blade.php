@extends('template.layout')

@section('content')
    <div class="row">
        <aside class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body p-2">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}">Users</a></li>
                    </ul>
                </div>
            </div>
        </aside>

        <section class="col-md-9">
            @yield('content')
        </section>
    </div>
@endsection