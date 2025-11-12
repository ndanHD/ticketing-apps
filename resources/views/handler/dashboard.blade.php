@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-speedometer2 me-2"></i>Handler Dashboard</h1>
        <p class="text-muted small mb-0">Ringkasan tiket yang ditugaskan ke Anda</p>
    </div>
</div>

<div class="row g-3 mb-3">
    @php
        $assigned = Auth::user()->ticketsAssigned()->count();
        $resolved = Auth::user()->ticketsAssigned()->where('status', 'resolved')->count();
        $open = Auth::user()->ticketsAssigned()->where('status', 'open')->count();
        $pending = Auth::user()->ticketsAssigned()->where('status', 'pending')->count();
    @endphp

    <div class="col-sm-6 col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 display-6 text-primary"><i class="bi bi-person-badge"></i></div>
                <div>
                    <div class="text-muted small">Assigned</div>
                    <div class="h4 mb-0">{{ $assigned }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 display-6 text-success"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="text-muted small">Resolved</div>
                    <div class="h4 mb-0">{{ $resolved }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 display-6 text-warning"><i class="bi bi-exclamation-circle"></i></div>
                <div>
                    <div class="text-muted small">Open</div>
                    <div class="h4 mb-0">{{ $open }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 display-6 text-secondary"><i class="bi bi-clock"></i></div>
                <div>
                    <div class="text-muted small">Pending</div>
                    <div class="h4 mb-0">{{ $pending }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Status Breakdown</h5>
                <small class="text-muted">Ringkasan</small>
            </div>
            <div class="card-body">
                <canvas id="handlerStatusChart" height="200"></canvas>
            </div>
        </div>

        <div class="card mt-3 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">Recent Assigned Tickets</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Created</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(Auth::user()->ticketsAssigned()->latest()->take(10)->get() as $ticket)
                            <tr>
                                <td>{{ $ticket->id }}</td>
                                <td>{{ Str::limit($ticket->title, 60) }}</td>
                                <td>
                                    @if($ticket->status == 'open')
                                        <span class="badge bg-warning">Open</span>
                                    @elseif($ticket->status == 'in_progress')
                                        <span class="badge bg-info">In Progress</span>
                                    @elseif($ticket->status == 'resolved')
                                        <span class="badge bg-success">Resolved</span>
                                    @elseif($ticket->status == 'pending')
                                        <span class="badge bg-secondary">Pending</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $ticket->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->priority == 'high')
                                        <span class="badge bg-danger">High</span>
                                    @elseif($ticket->priority == 'medium')
                                        <span class="badge bg-warning text-dark">Medium</span>
                                    @else
                                        <span class="badge bg-info">Low</span>
                                    @endif
                                </td>
                                <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('handler.tickets.show', $ticket->id) }}" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="mb-0">Quick Actions</h5>
                <p class="small text-muted">Pin akses cepat: buka tiket pending, lihat antrian, atur jadwal.</p>
                <div class="mt-3">
                    <a href="{{ route('handler.tickets.index') }}" class="btn btn-outline-primary btn-sm me-2">Daftar Tiket</a>
                    <a href="{{ route('handler.tickets.index') }}?filter=pending" class="btn btn-outline-secondary btn-sm">Tiket Pending</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.handlerCharts = window.handlerCharts || {};

        function initHandlerDashboard() {
            if (window.handlerCharts.statusChart) {
                window.handlerCharts.statusChart.destroy();
            }

            setTimeout(function() {
                const palette = ['#0d6efd','#ffc107','#198754','#6c757d'];
                const ctx = document.getElementById('handlerStatusChart');
                if (ctx && typeof Chart !== 'undefined') {
                    window.handlerCharts.statusChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: <?php echo json_encode(array_keys($statusCounts ?? [])); ?>,
                            datasets: [{
                                data: <?php echo json_encode(array_values($statusCounts ?? [])); ?>,
                                backgroundColor: palette,
                                borderColor: '#fff',
                                borderWidth: 1
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                    });
                }
            }, 100);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initHandlerDashboard);
        } else {
            initHandlerDashboard();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
@endsection