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
        <div id="pending-fields" class="mb-3" style="display: none;">
            <label class="form-label">Alasan Pending</label>
            <textarea name="pending_reason" class="form-control" rows="4">{{ old('pending_reason', $ticket->pending_reason ?? '') }}</textarea>
        </div>
        <div id="pending-until-field" class="mb-3" style="display: none;">
            <label class="form-label">Pending Hingga</label>
            @php
                $pendingUntilVal = old('pending_until', $ticket->pending_until ?? null);
                if ($pendingUntilVal) {
                    try {
                        $pendingUntilVal = \Carbon\Carbon::parse($pendingUntilVal)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // keep original if parse fails
                    }
                }
            @endphp
            <input type="date" name="pending_until" class="form-control" value="{{ $pendingUntilVal }}" />
        </div>
        <div class="mb-3">
            <label class="form-label">Detail</label>
            <textarea name="detail" class="form-control" rows="6" required>{{ old('detail', $ticket->detail) }}</textarea>
        </div>
        <button class="btn btn-primary">Simpan</button>
    </form>
    
    @php
        $isAdmin = optional(optional(auth()->user())->role)->name === 'admin';
    @endphp

    @push('scripts')
    <script>
        (function(){
            const statusEl = document.querySelector('select[name="status"]');
            const pendingFields = document.getElementById('pending-fields');
            const pendingUntil = document.getElementById('pending-until-field');
            const isAdmin = @json($isAdmin);
            function togglePending() {
                const val = statusEl.value;
                if (val === 'pending') {
                    pendingFields.style.display = '';
                    pendingUntil.style.display = '';
                    // if admin, set required on inputs
                    if (isAdmin) {
                        const pr = document.querySelector('textarea[name="pending_reason"]');
                        const pu = document.querySelector('input[name="pending_until"]');
                        if (pr) pr.setAttribute('required','required');
                        if (pu) pu.setAttribute('required','required');
                    }
                } else {
                    pendingFields.style.display = 'none';
                    pendingUntil.style.display = 'none';
                    try {
                        const pr = document.querySelector('textarea[name="pending_reason"]');
                        const pu = document.querySelector('input[name="pending_until"]');
                        if (pr) pr.removeAttribute('required');
                        if (pu) pu.removeAttribute('required');
                    } catch(e){}
                }
            }
            if (statusEl) {
                statusEl.addEventListener('change', togglePending);
                // initial
                togglePending();
            }
        })();
    </script>
    @endpush
@endsection
