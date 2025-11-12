@extends('admin.layout')

@section('title', 'Tiket Saya (Handler)')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Tiket yang Ditugaskan</h1>
        @if(isset($newCount) && $newCount > 0)
            <span class="badge bg-success">{{ $newCount }} Baru</span>
        @endif
    </div>

    <!-- Filter Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="ticketSearch" class="form-control" placeholder="Cari berdasarkan ID, judul, atau pembuat...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" id="status_filter">
                        <option value="">Semua Status</option>
                        <option value="open">Terbuka</option>
                        <option value="in_progress">Sedang Diproses</option>
                        <option value="pending">Pending</option>
                        <option value="closed">Selesai</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable -->
    <div class="table-responsive">
        <table id="ticketsTable" class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Modal Tutup Tiket -->
    <div class="modal fade" id="closeTicketModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('handler.tickets.close') }}" method="POST" onsubmit="return validateCloseForm(event)">
                    @csrf
                    <input type="hidden" name="ticket_id" id="modalTicketId" value="">
                    
                    <div class="modal-header">
                        <h5 class="modal-title">Tutup Tiket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <p id="modalTicketTitle" class="mb-3"></p>
                        
                        <div class="mb-3">
                            <label for="resolution_notes" class="form-label">Catatan Penyelesaian <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('resolution_notes') is-invalid @enderror" 
                                id="resolution_notes" 
                                name="resolution_notes" 
                                rows="4"
                                required
                                placeholder="Jelaskan bagaimana masalah ini diselesaikan..."></textarea>
                            @error('resolution_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Pastikan sebelum menutup tiket:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirmResolved" name="confirm_resolved" required>
                                <label class="form-check-label" for="confirmResolved">
                                    Saya konfirmasi bahwa masalah telah benar-benar diselesaikan
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Tutup Tiket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        let table;

        document.addEventListener('DOMContentLoaded', function () {
            console.log('Initializing Handler Tickets DataTable...');
            table = $('#ticketsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('handler.tickets.index') }}",
                    type: 'GET',
                    data: function (d) {
                        d.status = $('#status_filter').val();
                        console.log('Sending filter data:', d);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading data:', status, error, xhr.responseText);
                    }
                },
                columns: [
                    { data: 'ticket_id', name: 'id' },
                    { data: 'type', name: 'ticket_type_id' },
                    { data: 'status', name: 'status' },
                    { data: 'created_by', name: 'created_by' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 15,
                lengthMenu: [10, 15, 25, 50],
                dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip'
            });

            // Event listeners for filters
            $('#status_filter').on('change', function () {
                table.draw();
            });

            // Search functionality
            let searchTimeout;
            $('#ticketSearch').on('keyup', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    table.search($(this).val()).draw();
                }, 500);
            });

            // Handle modal data population
            const closeTicketModal = document.getElementById('closeTicketModal');
            if (closeTicketModal) {
                closeTicketModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const ticketId = button.getAttribute('data-ticket-id');
                    const ticketTitle = button.getAttribute('data-ticket-title');
                    
                    document.getElementById('modalTicketId').value = ticketId;
                    document.getElementById('modalTicketTitle').textContent = `Anda akan menutup: ${ticketTitle}`;
                });

                closeTicketModal.addEventListener('hidden.bs.modal', function() {
                    document.querySelector('#closeTicketModal form').reset();
                    document.getElementById('modalTicketTitle').textContent = '';
                });
            }

            function validateCloseForm(event) {
                const ticketId = document.getElementById('modalTicketId').value;
                if (!ticketId) {
                    alert('Error: No ticket ID found. Please try again.');
                    event.preventDefault();
                    return false;
                }
                return true;
            }
        });
    </script>
    @endpush

    @push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        .dataTables_wrapper .dataTables_filter {
            display: none;
        }
    </style>
    @endpush
@endsection
