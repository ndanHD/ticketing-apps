@extends('template.layout')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4 mb-0">Dashboard</h2>
            <p class="text-muted small mb-0">Ringkasan tiket Anda</p>
        </div>
        <div>
            <a href="{{ route('user.tickets.create') }}" class="btn btn-primary btn-sm">Buat Tiket Baru</a>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Tiket</div>
                    <div class="h4 mb-0">{{ $stats['total_tickets'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Tiket Terbuka</div>
                    <div class="h4 mb-0">{{ $stats['open_tickets'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Tiket Pending</div>
                    <div class="h4 mb-0">{{ $stats['pending_tickets'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Sedang Diproses</div>
                    <div class="h4 mb-0">{{ $stats['in_progress_tickets'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Tiket Selesai</div>
                    <div class="h4 mb-0">{{ $stats['closed_tickets'] }}</div>
                </div>
            </div>
        </div>
    </div>



    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0">Tiket Terbaru</h6>
            <a href="{{ route('user.tickets.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            @if($stats['recent_tickets']->isEmpty())
                <p class="p-3 text-muted">Belum ada tiket yang dibuat.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
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
                                    <td>{{ Str::limit($ticket->title, 60) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $ticket->status === 'open' ? 'warning text-dark' : ($ticket->status === 'closed' ? 'secondary' : 'info') }}">{{ $ticket->status }}</span>
                                    </td>
                                    <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                    <td><a href="{{ route('user.tickets.show', $ticket) }}" class="btn btn-sm btn-info">Lihat</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0 d-flex align-items-center justify-content-center">
            <div class="card shadow-sm w-100" style="max-width:320px;">
                <div class="card-body">
                    <h6 class="mb-3">Statistik Tiket</h6>
                    <div class="d-flex justify-content-center">
                        <canvas id="ticketStatusChart" width="180" height="180" style="max-width:180px;max-height:180px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center h-100">
                    <h6 class="mb-0">Tips</h6>
                    <p class="small text-muted mb-0">Sertakan informasi detail dan screenshot untuk mempercepat penanganan.</p>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var canvas = document.getElementById('ticketStatusChart');
    if (!canvas) {
        console.error('Canvas #ticketStatusChart tidak ditemukan');
        return;
    }
    if (typeof Chart === 'undefined') {
        console.error('Chart.js tidak termuat');
        return;
    }
    var ctx = canvas.getContext('2d');
    var data = {!! json_encode([$stats['open_tickets'] ?? 0, $stats['pending_tickets'] ?? 0, $stats['in_progress_tickets'] ?? 0, $stats['closed_tickets'] ?? 0]) !!};
    console.log('Data chart:', data);
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Terbuka', 'Pending', 'Sedang Diproses', 'Selesai'],
            datasets: [{
                data: data,
                backgroundColor: [
                    'rgba(255, 193, 7, 0.7)',    // warning
                    'rgba(255, 87, 34, 0.7)',    // orange (pending)
                    'rgba(13, 202, 240, 0.7)',   // info
                    'rgba(108, 117, 125, 0.7)'   // secondary
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
@endpush
@endsection
