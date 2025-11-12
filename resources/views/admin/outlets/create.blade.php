@extends('admin.layout')

@section('title', 'Tambah Outlet')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Tambah Outlet</h1>
        <a href="{{ route('admin.outlets.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <form action="{{ route('admin.outlets.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Outlet</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="address" class="form-control">{{ old('address') }}</textarea>
        </div>
        <button class="btn btn-primary">Simpan</button>
    </form>
@endsection
