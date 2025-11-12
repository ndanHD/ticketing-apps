@extends('template.layout')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Register</div>
            <div class="card-body">
                <form method="POST" action="{{ route('register.post') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="outlet_id" class="form-label">Pilih Outlet</label>
                        <select name="outlet_id" id="outlet_id" class="form-select @error('outlet_id') is-invalid @enderror">
                            <option value="">-- Pilih Outlet (Opsional) --</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ old('outlet_id') == $outlet->id ? 'selected' : '' }}>{{ $outlet->name }}{{ $outlet->address ? ' - ' . $outlet->address : '' }}</option>
                            @endforeach
                        </select>
                        @error('outlet_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('login.show') }}">Sudah punya akun? Login</a>
                        <button type="submit" class="btn btn-primary">Daftar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
