@extends('admin.layout')

@section('title', 'Detail Tiket')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Detail Tiket #{{ $ticket->id }}</h1>
        <div>
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary">Kembali</a>
            <a href="{{ route('admin.tickets.edit', $ticket) }}" class="btn btn-secondary">Edit</a>
            @if($ticket->status !== 'pending')
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#pendingModal">Set Pending</button>
            @else
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#pendingModal">Ubah Pending</button>
                <button type="button" class="btn btn-outline-danger ms-2" data-bs-toggle="modal" data-bs-target="#adminClosePendingModal">Close Pending</button>
            @endif
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h5 class="mb-3">{{ $ticket->title }}</h5>
                    <p><strong>Tipe:</strong> {{ $ticket->ticketType->name ?? '-' }}</p>
                    <p><strong>SLA:</strong> {{ $ticket->sla->name ?? '-' }}</p>
                    <p>
                        <strong>Status:</strong>
                        <span
                            class="badge bg-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'closed' ? 'secondary' : 'warning') }}">
                            {{ $ticket->status }}
                        </span>
                    </p>
                </div>
                <div class="col-md-4">
                    <p><strong>Created By:</strong> {{ $ticket->createdBy->name ?? '-' }}</p>
                    <p><strong>Tanggal:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                    <p>
                        <strong>Prioritas:</strong>
                        <span
                            class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'info') }}">
                            {{ $ticket->priority }}
                        </span>
                    </p>
                    <p>
                        <strong>Rating:</strong>
                        @if (isset($ratingsCount) && $ratingsCount > 0)
                            @include('components.star-rating', [
                                'rating' => $ratingsAvg,
                                'count' => $ratingsCount,
                            ])
                        @else
                            <span class="text-muted">Belum ada rating</span>
                        @endif
                    </p>
                </div>
            </div>

            @if ($ticket->status === 'open' && !$ticket->assign_to)
                <div class="alert alert-warning mb-4">
                    <h6 class="alert-heading">Tiket Belum Ditugaskan</h6>
                    <p class="mb-0">Silakan pilih handler untuk menangani tiket ini.</p>
                </div>

                <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST" class="card bg-light mb-4">
                    <div class="card-body">
                        <h6 class="card-title">Tugaskan ke Handler:</h6>
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8">
                                <select name="handler_id" class="form-select @error('handler_id') is-invalid @enderror"
                                    required>
                                    <option value="">-- Pilih Handler --</option>
                                    @foreach ($handlers as $handler)
                                        <option value="{{ $handler->id }}">
                                            {{ $handler->name }} ({{ $handler->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('handler_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">Tugaskan Tiket</button>
                            </div>
                        </div>
                    </div>
                </form>
            @elseif($ticket->assignTo)
                <div class="alert alert-info mb-4">
                    <h6 class="alert-heading">Informasi Handler</h6>
                    <p class="mb-0">
                        Tiket ditangani oleh: <strong>{{ $ticket->assignTo->name }}</strong><br>
                        Email: {{ $ticket->assignTo->email }}
                    </p>
                </div>
            @endif

            <div class="ticket-detail border-top pt-3">
                <h6>Detail Masalah:</h6>
                <textarea id="ticket-description" readonly>{{ $ticket->description }}</textarea>
            </div>
        </div>
    </div>

            <!-- Pending Modal -->
            <div class="modal fade" id="pendingModal" tabindex="-1" aria-labelledby="pendingModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.tickets.update', $ticket) }}" method="POST" id="pending-form">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title" id="pendingModalLabel">Set Tiket Pending</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="status" value="pending">

                                <div class="mb-3">
                                    <label for="pending_reason" class="form-label">Alasan Pending</label>
                                    <textarea name="pending_reason" id="pending_reason" class="form-control" rows="4" required>{{ old('pending_reason', $ticket->pending_reason ?? '') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="pending_until" class="form-label">Pending Hingga</label>
                                    <input type="date" name="pending_until" id="pending_until" class="form-control" value="{{ old('pending_until', optional($ticket->pending_until)->format('Y-m-d')) }}" required />
                                </div>

                                <div class="alert alert-secondary small">Catatan: field ini wajib diisi saat mengubah status menjadi <strong>pending</strong>.</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning">Simpan Pending</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
                            <!-- Admin Close Pending Modal -->
                            <div class="modal fade" id="adminClosePendingModal" tabindex="-1" aria-labelledby="adminClosePendingModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.tickets.pending.close', $ticket) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="adminClosePendingModalLabel">Hapus Pending Tiket</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Anda akan menghapus status <strong>PENDING</strong> untuk tiket ini.</p>
                                                <p>Setelah dihapus, status akan berubah menjadi <strong>in_progress</strong> jika tiket sudah ditugaskan ke handler, atau <strong>open</strong> jika belum ditugaskan.</p>
                                                <div class="mb-3">
                                                    <label for="admin_close_note" class="form-label">Catatan (opsional)</label>
                                                    <textarea id="admin_close_note" name="note" class="form-control" rows="3" placeholder="Catatan singkat untuk riwayat..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Hapus Pending</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

    <h4>Komentar</h4>
    <div class="mb-3">
        <form action="{{ route('admin.tickets.comments.store', $ticket) }}" method="POST">
            @csrf
            <div class="mb-2">
                <textarea name="comment" class="form-control" rows="3" placeholder="Tulis komentar..."></textarea>
            </div>
            <button class="btn btn-primary">Kirim Komentar</button>
        </form>
    </div>

    <div>
        @forelse($ticket->comments as $comment)
            <div class="card mb-2">
                <div class="card-body">
                    <strong>{{ $comment->user->name ?? 'Guest' }}</strong>
                    <p class="mb-0">{{ $comment->comment }}</p>
                    <small class="text-muted">{{ $comment->created_at->format('Y-m-d H:i') }}</small>
                </div>
            </div>
        @empty
            <p>Belum ada komentar.</p>
        @endforelse
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#ticket-description'), {
                toolbar: [], // Hilangkan semua tombol
                readOnly: true, // Non-editable
            })
            .then(editor => {
                editor.enableReadOnlyMode('ticket-description');
                // Tambahkan class img-fluid agar gambar responsive
                document.querySelectorAll('.ck-content img').forEach(img => {
                    img.classList.add('img-fluid');
                });
            })
            .catch(error => console.error(error));
    </script>
@endpush
