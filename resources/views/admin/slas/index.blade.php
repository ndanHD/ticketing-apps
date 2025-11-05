@extends('admin.layout')

@section('title', 'Manajemen SLA')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manajemen SLA</h1>
            <a href="{{ route('admin.slas.create') }}" class="btn btn-primary">
                Tambah SLA Baru
            </a>
        </div>

        @if($slas->isEmpty())
            <div class="alert alert-info">
                Belum ada data SLA. Silakan tambahkan SLA baru.
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama SLA</th>
                                    <th>Response Time (jam)</th>
                                    <th>Resolution Time (jam)</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($slas as $sla)
                                    <tr>
                                        <td>{{ $sla->name }}</td>
                                        <td>{{ $sla->response_time }}</td>
                                        <td>{{ $sla->resolution_time }}</td>
                                        <td>{{ $sla->description ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.slas.edit', $sla) }}" class="btn btn-sm btn-info">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.slas.destroy', $sla) }}" 
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
                        {{ $slas->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection