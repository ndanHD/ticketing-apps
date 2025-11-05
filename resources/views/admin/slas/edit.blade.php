@extends('admin.layout')

@section('title', 'Edit SLA')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit SLA</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.slas.update', $sla) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama SLA</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name', $sla->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="response_time" class="form-label">Response Time (jam)</label>
                                <input type="number" class="form-control @error('response_time') is-invalid @enderror" 
                                    id="response_time" name="response_time" 
                                    value="{{ old('response_time', $sla->response_time) }}" 
                                    min="1" required>
                                @error('response_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="resolution_time" class="form-label">Resolution Time (jam)</label>
                                <input type="number" class="form-control @error('resolution_time') is-invalid @enderror" 
                                    id="resolution_time" name="resolution_time" 
                                    value="{{ old('resolution_time', $sla->resolution_time) }}" 
                                    min="1" required>
                                @error('resolution_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                    id="description" name="description" rows="3">{{ old('description', $sla->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.slas.index') }}" class="btn btn-outline-secondary">
                                    Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Update SLA
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection