@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Admin Dashboard</h1>
            <p class="text-muted small mb-0">Ringkasan operasional & statistik tiket</p>
        </div>
    </div>

    <div class="row g-3 mb-3">
        @php
            $total = array_sum($statusCounts ?? []);
            $open = $statusCounts['open'] ?? 0;
            $inProgress = $statusCounts['in_progress'] ?? 0;
            $pending = $statusCounts['pending'] ?? 0;
            $resolved = $statusCounts['resolved'] ?? 0;
        @endphp

        <div class="col-sm-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 display-6 text-primary">
                        <i class="bi bi-card-checklist"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Tiket</div>
                        <div class="h4 mb-0">{{ $total }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 display-6 text-warning">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
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
                    <div class="me-3 display-6 text-info">
                        <i class="bi bi-gear-wide"></i>
                    </div>
                    <div>
                        <div class="text-muted small">In Progress</div>
                        <div class="h4 mb-0">{{ $inProgress }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-2">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 display-6 text-secondary">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Pending</div>
                        <div class="h4 mb-0">{{ $pending }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-2">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 display-6 text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Closed</div>
                        <div class="h4 mb-0">{{ $resolved }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Status Tiket</h5>
                    <small class="text-muted">Perbandingan status</small>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>

            @if(!empty($recentTickets))
            <div class="card mt-3 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Tiket Terbaru</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Judul</th>
                                    <th>Outlet</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTickets as $t)
                                <tr>
                                    <td>{{ $t->id }}</td>
                                    <td>{{ Str::limit($t->title, 60) }}</td>
                                    <td>{{ $t->outlet->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $t->status === 'open' ? 'warning text-dark' : ($t->status === 'resolved' ? 'success' : 'secondary') }}">{{ $t->status }}</span>
                                    </td>
                                    <td>{{ $t->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Tiket per Outlet</h5>
                    <small class="text-muted">Distribusi</small>
                </div>
                <div class="card-body">
                    <canvas id="outletChart" height="300"></canvas>
                </div>
            </div>

            <div class="card mt-3 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-0">Catatan</h6>
                    <p class="small text-muted mb-0">Gunakan filter dan laporan untuk mengekspor data lebih detail.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Global chart instances for admin dashboard
        window.adminCharts = window.adminCharts || {};

        function initAdminDashboard() {
            // Destroy existing charts
            if (window.adminCharts.statusChart) {
                window.adminCharts.statusChart.destroy();
            }
            if (window.adminCharts.outletChart) {
                window.adminCharts.outletChart.destroy();
            }

            // Initialize charts after a short delay to ensure Chart.js is loaded
            setTimeout(function() {
                const palette = ['#0d6efd','#ffc107','#198754','#6c757d','#0dcaf0','#fd7e14'];
                
                // Status Chart
                const ctx1 = document.getElementById('statusChart');
                if (ctx1 && typeof Chart !== 'undefined') {
                    window.adminCharts.statusChart = new Chart(ctx1, {
                        type: 'doughnut',
                        data: {
                            labels: <?php echo json_encode(array_keys($statusCounts ?? [])); ?>,
                            datasets: [{
                                data: <?php echo json_encode(array_values($statusCounts ?? [])); ?>,
                                backgroundColor: palette,
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                }

                // Outlet Chart
                const ctx2 = document.getElementById('outletChart');
                if (ctx2 && typeof Chart !== 'undefined') {
                    window.adminCharts.outletChart = new Chart(ctx2, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode(array_keys($outletCounts ?? [])); ?>,
                            datasets: [{
                                label: 'Jumlah Tiket',
                                data: <?php echo json_encode(array_values($outletCounts ?? [])); ?>,
                                backgroundColor: palette[0]
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { x: { beginAtZero: true } }
                        }
                    });
                }
            }, 100);
        }

        // Initialize on DOMContentLoaded and also load Chart.js first
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAdminDashboard);
        } else {
            initAdminDashboard();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush