@extends('admin.layout')

@section('title', 'Edit User')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Edit User</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    {{-- =======================
         User Details Form
    ======================== --}}
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h5 class="card-title mb-4">User Information</h5>

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Job Title</label>
                    <input type="text" name="job_tittle" class="form-control @error('job_tittle') is-invalid @enderror"
                        value="{{ old('job_tittle', $user->job_tittle) }}" required>
                    @error('job_tittle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Outlet</label>
                    <select name="outlet_id" class="form-select @error('outlet_id') is-invalid @enderror" required>
                        <option value="">Select Outlet</option>
                        @foreach ($outlets as $outlet)
                            <option value="{{ $outlet->id }}"
                                {{ old('outlet_id', $user->outlet_id) == $outlet->id ? 'selected' : '' }}>
                                {{ $outlet->name }}{{ $outlet->address ? ' - ' . $outlet->address : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('outlet_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Role</label>
                    <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                        <option value="">Select Role</option>
                        @foreach ($availableRoles as $role)
                            <option value="{{ $role->id }}"
                                {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                        @checked(old('is_active', $user->is_active))>
                </div>


                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

    {{-- =======================
         Password Reset Section
    ======================== --}}
    <div class="card border-warning shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-warning mb-3">
                <i class="bi bi-shield-lock"></i> Reset User Password
            </h5>
            <p class="text-muted mb-4">
                Clicking the button below will reset this user's password to a new random one.
                The new password will be automatically sent to the user's email address.
            </p>

            <form action="{{ route('admin.users.reset-password', $user) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to reset this user\'s password? The new password will be emailed to them.')">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-arrow-repeat"></i> Reset Password
                </button>
            </form>
        </div>
    </div>
@endsection
