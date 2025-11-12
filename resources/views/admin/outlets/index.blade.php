@extends('admin.layout')

@section('title', 'Daftar Outlet')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Daftar Outlet</h1>
        <a href="{{ route('admin.outlets.create') }}" class="btn btn-primary">Tambah Outlet</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($outlets as $outlet)
                <tr>
                    <td>{{ $outlet->name }}</td>
                    <td>{{ $outlet->address }}</td>
                    <td>-</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $outlets->links() }}
@endsection
