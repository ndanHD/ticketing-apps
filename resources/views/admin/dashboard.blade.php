@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center py-2">
        <div>
            <h1 class="h3 mb-0">Admin Dashboard</h1>
            <p class="">Ringkasan operasional & statistik tiket</p>
        </div>
    </div>

    <div class="row wrapper-card g-2">
        @php
            $total = array_sum($statusCounts ?? []);
            $open = $statusCounts['open'] ?? 0;
            $inProgress = $statusCounts['in_progress'] ?? 0;
            $pending = $statusCounts['pending'] ?? 0;
            $resolved = $statusCounts['resolved'] ?? 0;
        @endphp
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-6 mb-3 px-1">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-evenly flex-column text-info fw-bold">
                    <div class="display-6">
                        <i class="bi bi-card-checklist"></i>
                    </div>
                    <div class="">Total Tiket</div>
                    <div class="h4 mb-0">{{ $total }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-6 mb-3 px-1">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-evenly flex-column text-primary fw-bold">
                    <div class="display-6">
                        <i class="fa-solid fa-envelope-open"></i>
                        <!-- <i class="bi bi-exclamation-circle"></i> -->
                    </div>
                    <div>Open</div>
                    <div class="h4 mb-0">{{ $open }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-6 mb-3 px-1">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-evenly flex-column fw-bold" style="color">
                    <div class="display-6 text-secondary">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                    <div class="text-muted small">In Progress</div>
                    <div class="h4 mb-0">{{ $inProgress }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-6 mb-3 px-1">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-evenly flex-column text-warning fw-bold">
                    <div class="display-6">
                        <i class="fa-regular fa-circle-question"></i>
                    </div>
                    <div class="">Pending</div>
                    <div class="h4 mb-0">{{ $pending }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-6 mb-3 px-1">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-evenly flex-column text-danger fw-bold">
                    <div class="display-6">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <div class="">Overdue</div>
                    <div class="h4 mb-0">{{ $resolved }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-6 mb-3 px-1">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-evenly flex-column text-success fw-bold">
                    <div class="display-6">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="">Closed</div>
                    <div class="h4 mb-0">{{ $resolved }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-6">
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
                                                <span
                                                    class="badge bg-{{ $t->status === 'open' ? 'warning text-dark' : ($t->status === 'resolved' ? 'success' : 'secondary') }}">{{ $t->status }}</span>
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
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Pie Chart</h5>
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
                                                <span
                                                    class="badge bg-{{ $t->status === 'open' ? 'warning text-dark' : ($t->status === 'resolved' ? 'success' : 'secondary') }}">{{ $t->status }}</span>
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
    </div>
    <div class="row g-3">
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Tiket per Outlet</h5>
                    <small class="text-muted">Distribusi</small>
                </div>
                <div class="card-body">
                    <canvas id="outletChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Line Chart</h5>
                    <small class="text-muted">Distribusi</small>
                </div>
                <div class="card-body">
                    <canvas id="pieChart" height="300"></canvas>
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
            setTimeout(function () {
                const palette = ['#0d6efd', '#ffc107', '#198754', '#6c757d', '#0dcaf0', '#fd7e14'];

                // Status Chart
                const ctx1 = document.getElementById('statusChart');
                if (ctx1 && typeof Chart !== 'undefined') {
                    window.adminCharts.statusChart = new Chart(ctx1, {
                        type: 'pie',
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
                            indexAxis: 'x',
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { x: { beginAtZero: true } }
                        }
                    });
                }
                // Pie Chart
                const data = {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                    datasets: [{
                        label: 'My First Dataset',
                        data: [65, 59, 80, 81, 56, 55, 40],
                        fill: false,
                        borderColor: 'rgb(750, 192, 192)',
                        tension: 0.1
                    }]
                };
                const ctx3 = document.getElementById('pieChart');
                if (ctx3 && typeof Chart !== 'undefined') {
                    window.adminCharts.pieChart = new Chart(ctx3, {
                        type: 'line',
                        data: data,
                        options: {
                            indexAxis: 'x',
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