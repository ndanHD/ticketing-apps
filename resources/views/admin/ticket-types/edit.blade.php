@extends('admin.layout')

@section('title', 'Edit Tipe Tiket')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Tipe Tiket</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.ticket-types.update', $ticketType) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Tipe</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name', $ticketType->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                    id="description" name="description" rows="3">{{ old('description', $ticketType->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" 
                                    name="is_active" value="1" 
                                    {{ old('is_active', $ticketType->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.ticket-types.index') }}" class="btn btn-outline-secondary">
                                    Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Update Tipe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection