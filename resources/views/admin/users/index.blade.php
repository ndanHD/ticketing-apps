@extends('admin.layout')

@section('title', 'Manajemen User')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Manajemen User</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Tambah User
        </a>
    </div>

    <div class="table-responsive">
        <table id="usersTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Jabatan</th>
                    <th>Role</th>
                    <th>Tanggal Dibuat</th>
                    <th>Status</th>
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

        document.addEventListener('DOMContentLoaded', function () {
            console.log('Initializing Users DataTable...');
            table = $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('admin.users.index') }}",
                    type: 'GET',
                    error: function (xhr, status, error) {
                        console.error('Error loading data:', status, error, xhr.responseText);
                    }
                },
                columns: [
                    { data: 'id_short', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'job_title', name: 'job_tittle' },
                    { data: 'role', name: 'role_id' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'status', name: 'is_active', orderable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 15,
                lengthMenu: [10, 15, 25, 50],
                dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip'
            });

            // Handle delete with SweetAlert
            $(document).on('submit', '.swal-delete', function (e) {
                e.preventDefault();
                const form = this;
                const userName = $(form).data('name');

                Swal.fire({
                    title: 'Hapus User?',
                    text: 'User ' + userName + ' akan dihapus secara permanen',
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
    </script>
    @endpush
@endsection
