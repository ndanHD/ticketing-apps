@extends('template.layout')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Dashboard</h2>

    <!-- Statistik Tiket -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Tiket</h5>
                    <h2 class="card-text mb-0">{{ $stats['total_tickets'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Tiket Terbuka</h5>
                    <h2 class="card-text mb-0">{{ $stats['open_tickets'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Sedang Diproses</h5>
                    <h2 class="card-text mb-0">{{ $stats['in_progress_tickets'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Tiket Selesai</h5>
                    <h2 class="card-text mb-0">{{ $stats['closed_tickets'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tiket Terbaru -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tiket Terbaru</h5>
            <a href="{{ route('user.tickets.create') }}" class="btn btn-primary btn-sm">Buat Tiket Baru</a>
        </div>
        <div class="card-body">
            @if($stats['recent_tickets']->isEmpty())
                <p class="text-muted">Belum ada tiket yang dibuat.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipe</th>
                                <th>Subjek</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_tickets'] as $ticket)
                                <tr>
                                    <td>{{ $ticket->id }}</td>
                                    <td>{{ $ticket->ticketType->name ?? '-' }}</td>
                                    <td>{{ $ticket->title }}</td>
                                    <td>
                                        <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'closed' ? 'secondary' : 'warning') }}">
                                            {{ $ticket->status }}
                                        </span>
                                    </td>
                                    <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('user.tickets.show', $ticket) }}" class="btn btn-sm btn-info">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="text-end mt-3">
                    <a href="{{ route('user.tickets.index') }}" class="btn btn-outline-primary">
                        Lihat Semua Tiket
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
