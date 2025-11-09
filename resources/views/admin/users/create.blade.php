@extends('admin.layout')

@section('title', 'Create User')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Create User</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Job Tittle</label>
            <input type="job_tittle" name="job_tittle" class="form-control @error('job_tittle') is-invalid @enderror"
                value="{{ old('job_tittle') }}" required>
            @error('job_tittle')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Outlet</label>
            <select name="outlet_id" class="form-select @error('outlet_id') is-invalid @enderror" required>
                <option value="">Select Outlet</option>
                @foreach ($outlets as $outlet)
                    <option value="{{ $outlet->id }}" {{ old('outlet_id') == $outlet->id ? 'selected' : '' }}>
                        {{ $outlet->name }}{{ $outlet->address ? ' - ' . $outlet->address : '' }}
                    </option>
                @endforeach
            </select>
            @error('outlet_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                <option value="">Select Role</option>
                @foreach ($availableRoles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            @error('role_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Create User</button>
    </form>
@endsection
