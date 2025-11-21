@extends('admin.layout')

@section('title', 'Daftar Tiket')


@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Daftar Tiket</h1>
    </div>

    <div class="card main-card flex-row col-xl-12 col-lg-12 col-md-12 mb-0">
        <div class="card-start col-xl-4 col-lg-4 col-md-4">
            <div class="wrapper-search-filter d-flex align-items-center justify-content-between">
                <div class="form-group align-items-center justify-content-center py-3 ps-4">
                    <div class="form-line">
                        <input type="text" placeholder="Search" class="form-control">
                    </div>
                </div>
                <div class="wrapper-filter pe-4">
                    <i class="fa-solid fa-filter" style="font-size: 20px;"></i>
                </div>
            </div>
            <div class="wrapper-filter-status d-flex my-2">
                <div class="wrapper-status-all">
                    <i class="fa-solid fa-user"></i>
                    <span>All</span>
                </div>
                <div class="wrapper-status-unserved">
                    <i class="fa-solid fa-circle-exclamation"></i> <span>Unserved</span>
                </div>
                <div class="wrapper-status-reserved">
                    <i class="fa-solid fa-hourglass-half"></i>
                    <span>Reserved</span>
                </div>
                <div class="wrapper-status-solved">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Solved</span>
                </div>
            </div>
            <div class="wrapper-tickets-all">
                <div class="wrapper-ticket p-4">
                    <div class="ticket-header d-flex justify-content-between">
                        <span>Name</span>
                        <span>Date</span>
                    </div>
                    <div class="ticket-body d-flex justify-content-between mt-2">
                        <span>ticket tittle</span>
                        <span>priorty</span>
                        <span>type</span>
                        <span>handler</span>
                    </div>
                </div>
                <div class="wrapper-ticket p-4">
                    <div class="ticket-header d-flex justify-content-between">
                        <span>Name</span>
                        <span>Date</span>
                    </div>
                    <div class="ticket-body d-flex justify-content-between mt-2">
                        <span>ticket tittle</span>
                        <span>priorty</span>
                        <span>type</span>
                        <span>handler</span>
                    </div>
                </div>
                <div class="wrapper-ticket p-4">
                    <div class="ticket-header d-flex justify-content-between">
                        <span>Name</span>
                        <span>Date</span>
                    </div>
                    <div class="ticket-body d-flex justify-content-between mt-2">
                        <span>ticket tittle</span>
                        <span>priorty</span>
                        <span>type</span>
                        <span>handler</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-mid row col-xl-4 col-lg-4 col-md-4">
            <div class="wrapper-head-chat">
                head chat
            </div>
            <div class="wrapper-body-chat">
                body chat
            </div>
        </div>
        <div class="card-end row col-xl-4 col-lg-4 col-md-4">
            <div class="ticket-info-head">
                ticket info head
            </div>
            <div class="ticket-info-body">
                .ticket-info-body
            </div>
        </div>
@endsection





    @section('test')
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

                document.addEventListener('DOMContentLoaded', function () {
                    console.log('Initializing DataTable...');
                    table = $('#ticketsTable').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: {
                            url: "{{ route('admin.tickets.index') }}",
                            type: 'GET',
                            data: function (d) {
                                d.outlet_id = $('#outlet_id').val();
                                d.status = $('#status_filter').val();
                                d.priority = $('#priority_filter').val();
                                console.log('Sending filter data:', d);
                            },
                            error: function (xhr, status, error) {
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
                    $('#outlet_id, #status_filter, #priority_filter').on('change', function () {
                        table.draw();
                    });

                    // Handle delete with SweetAlert
                    $(document).on('submit', '.swal-delete', function (e) {
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