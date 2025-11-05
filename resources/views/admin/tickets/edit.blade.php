@extends('admin.layout')

@section('title', 'Edit Tiket')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Edit Tiket</h1>
        <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <form action="{{ route('admin.tickets.update', $ticket) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Tipe Tiket</label>
            <select name="ticket_type_id" class="form-select" required>
                <option value="">-- Pilih Tipe --</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ $ticket->ticket_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">SLA</label>
            <select name="sla_id" class="form-select" required>
                <option value="">-- Pilih SLA --</option>
                @foreach($slas as $sla)
                    <option value="{{ $sla->id }}" {{ $ticket->sla_id == $sla->id ? 'selected' : '' }}>{{ $sla->name }} ({{ $sla->duration }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Assign To</label>
            <select name="assign_to" class="form-select">
                <option value="">-- Tidak ada --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $ticket->assign_to == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Detail</label>
            <textarea name="detail" class="form-control" rows="6" required>{{ old('detail', $ticket->detail) }}</textarea>
        </div>
        <button class="btn btn-primary">Simpan</button>
    </form>
@endsection
