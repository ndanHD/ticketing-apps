@extends('template.layout')

@section('title', 'Tiket Saya')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tiket Saya</h2>
        <a href="{{ route('user.tickets.create') }}" class="btn btn-primary">
            Buat Tiket Baru
        </a>
    </div>

    <!-- DataTable -->
    <div class="table-responsive">
        <table id="ticketsTable" class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipe</th>
                    <th>Subjek</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        let table;

        document.addEventListener('DOMContentLoaded', function () {
            console.log('Initializing User Tickets DataTable...');
            table = $('#ticketsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('user.tickets.index') }}",
                    type: 'GET',
                    error: function (xhr, status, error) {
                        console.error('Error loading data:', status, error, xhr.responseText);
                    }
                },
                columns: [
                    { data: 'ticket_id', name: 'id' },
                    { data: 'type', name: 'ticket_type_id' },
                    { data: 'title', name: 'title' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                lengthMenu: [10, 25, 50],
                dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip'
            });

            // Bind custom search input to DataTables global search with debounce
            let searchTimeout;
            $('#globalSearch').on('keyup', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    table.search($(this).val()).draw();
                }, 300);
            });
        });
    </script>
    @endpush

    @push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    @endpush
</div>
@endsection
