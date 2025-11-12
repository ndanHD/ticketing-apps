@extends('admin.layout')

@section('title', 'Buat Tiket')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Buat Tiket</h1>
        <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <form action="{{ route('admin.tickets.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tipe Tiket</label>
            <select name="ticket_type_id" class="form-select" required>
                <option value="">-- Pilih Tipe --</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">SLA</label>
            <select name="sla_id" class="form-select" required>
                <option value="">-- Pilih SLA --</option>
                @foreach($slas as $sla)
                    <option value="{{ $sla->id }}">{{ $sla->name }} ({{ $sla->duration }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Buat Tiket untuk User</label>
            <select name="created_by" class="form-select">
                <option value="">-- Buat sebagai Admin --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
            <small class="form-text text-muted">
                Jika dipilih, tiket akan dibuat atas nama user tersebut.
            </small>
        </div>

        <div class="mb-3">
            <label class="form-label">Assign To</label>
            <select name="assign_to" class="form-select">
                <option value="">-- Tidak ada --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Detail</label>
            <textarea name="description" class="form-control" rows="6" required>{{ old('description') }}</textarea>
        </div>
        @if(!empty($outlets))
            <div class="mb-3">
                <label class="form-label">Outlet</label>
                <select name="outlet_id" class="form-select">
                    <option value="">-- Pilih Outlet (Default: Semua) --</option>
                    @foreach($outlets as $o)
                        <option value="{{ $o->id }}">{{ $o->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <button class="btn btn-primary">Simpan</button>
    </form>
@endsection
