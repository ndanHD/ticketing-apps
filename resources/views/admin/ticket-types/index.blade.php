@extends('admin.layout')

@section('title', 'Manajemen Tipe Tiket')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manajemen Tipe Tiket</h1>
            <a href="{{ route('admin.ticket-types.create') }}" class="btn btn-primary">
                Tambah Tipe Tiket
            </a>
        </div>

        @if($types->isEmpty())
            <div class="alert alert-info">
                Belum ada data tipe tiket. Silakan tambahkan tipe tiket baru.
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($types as $type)
                                    <tr>
                                        <td>{{ $type->name }}</td>
                                        <td>{{ $type->description ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $type->is_active ? 'success' : 'danger' }}">
                                                {{ $type->is_active ? 'Aktif' : 'Non-aktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.ticket-types.edit', $type) }}" class="btn btn-sm btn-info">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.ticket-types.destroy', $type) }}" 
                                                  method="POST" 
                                                  class="d-inline swal-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $types->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection