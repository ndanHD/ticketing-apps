@extends('admin.layout')

@section('title', 'Daftar Tiket')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Daftar Tiket</h1>
    <a href="{{ route('admin.tickets.create') }}" class="btn btn-primary">Buat Tiket</a>
</div>

    <!-- Filter Section -->
    <div class="card mb-4 shadow-sm" @if(empty($outlets)) style="display: none;" @endif>
        <div class="card-body">
            <div class="row g-3">
                @if(!empty($outlets))
                <div class="col-md-3">
                    <label for="outlet_id" class="form-label">Pilih Outlet</label>
                    <select class="form-select form-select-sm" id="outlet_id" name="outlet_id">
                        <option value="">Semua Outlet</option>
                        @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </div>
    </div><!-- DataTable -->
<div class="table-responsive">
    <table id="ticketsTable" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Judul</th>
                <th>Tipe</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Prioritas</th>
                <th>Dibuat Oleh</th>
                <th>Alasan Pending</th>
                <th>Pending Hingga</th>
                <th>Assign to</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    let table;

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initializing DataTable...');
        table = $('#ticketsTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('admin.tickets.index') }}",
                type: 'GET',
                data: function(d) {
                    d.outlet_id = $('#outlet_id').val();
                    d.status = $('#status_filter').val();
                    d.priority = $('#priority_filter').val();
                    console.log('Sending filter data:', d);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading data:', status, error, xhr.responseText);
                }
            },
            columns: [{
                    data: 'ticket_id',
                    name: 'id'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'type',
                    name: 'ticket_type_id'
                },
                {
                    data: 'rating',
                    name: 'rating',
                    orderable: false
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'priority',
                    name: 'priority'
                },
                {
                    data: 'created_by',
                    name: 'created_by'
                },
                {
                    data: 'pending_reason',
                    name: 'pending_reason',
                    orderable: false
                },
                {
                    data: 'pending_until',
                    name: 'pending_until'
                },
                {
                    data: 'assign_to',
                    name: 'assign_to',
                    orderable: false
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            pageLength: 20,
            lengthMenu: [10, 20, 50, 100],
            dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip'
        });

        // Event listeners for filters
        $('#outlet_id, #status_filter, #priority_filter').on('change', function() {
            table.draw();
        });

        // Handle delete with SweetAlert
        $(document).on('submit', '.swal-delete', function(e) {
            e.preventDefault();
            const form = this;
            const ticketId = $(form).data('name');

            Swal.fire({
                title: 'Hapus Tiket?',
                text: 'Tiket #' + ticketId + ' akan dihapus secara permanen',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    function reloadTable() {
        table.draw();
    }

    function resetFilters() {
        document.getElementById('outlet_id')?.querySelectorAll('option')[0]?.selected || $('#outlet_id').val('');
        $('#status_filter').val('');
        $('#priority_filter').val('');
        table.draw();
    }
</script>
@endpush
@endsection