@extends('admin.layout')

@section('title', 'Detail Tiket')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Detail Tiket #{{ $ticket->id }}</h2>
                <div class="btn-group">
                    <a href="{{ route('handler.tickets.index') }}" class="btn btn-outline-secondary">
                        Kembali ke Daftar
                    </a>
                    @if($ticket->status !== 'closed')
                        <button type="button" class="btn btn-success" 
                            data-bs-toggle="modal" 
                            data-bs-target="#closeTicketModal"
                            data-ticket-id="{{ $ticket->id }}"
                            data-ticket-title="{{ $ticket->title ?? 'Tiket #' . $ticket->id }}">
                            Tutup Tiket
                        </button>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ $ticket->title }}</h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'closed' ? 'secondary' : 'warning') }}">
                            {{ $ticket->status }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Tipe:</strong> {{ $ticket->ticketType->name ?? '-' }}</p>
                            <p><strong>SLA:</strong> {{ $ticket->sla->name ?? '-' }}</p>
                            <p><strong>Prioritas:</strong> 
                                <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'info') }}">
                                    {{ $ticket->priority }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p><strong>Dibuat oleh:</strong> {{ $ticket->createdBy->name ?? '-' }}</p>
                            <p><strong>Tanggal dibuat:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                            @if($ticket->closed_at)
                                <p><strong>Ditutup pada:</strong> {{ $ticket->closed_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="ticket-detail border-top pt-3">
                        <h6>Detail Masalah:</h6>
                        <p class="mb-0">{!! nl2br(e($ticket->description)) !!}</p>
                    </div>
                </div>
            </div>

            <!-- Komentar -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Diskusi</h5>
                </div>
                <div class="card-body">
                    @if($ticket->status !== 'closed')
                        <form action="{{ route('handler.tickets.comments.store', $ticket) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <label for="comment" class="form-label">Tambah Komentar</label>
                                <textarea class="form-control @error('comment') is-invalid @enderror" 
                                    id="comment" name="comment" rows="3" required
                                    placeholder="Tulis komentar atau informasi tambahan..."></textarea>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Kirim</button>
                        </form>
                    @endif

                    <div class="comments-list">
                        @forelse($ticket->comments->sortByDesc('created_at') as $comment)
                            <div class="comment-item mb-3 p-3 border rounded 
                                {{ $comment->user->id === auth()->id() ? 'bg-light' : '' }}
                                {{ $comment->is_resolution_note ? 'bg-success bg-opacity-10 border-success' : '' }}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong>{{ $comment->user->name }}</strong>
                                        @if($comment->is_resolution_note)
                                            <span class="badge bg-success ms-2">Catatan Penyelesaian</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <p class="mb-0">{{ $comment->comment }}</p>
                            </div>
                        @empty
                            <p class="text-muted">Belum ada komentar.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tutup Tiket -->
<div class="modal fade" id="closeTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
                <form action="{{ route('handler.tickets.close') }}" method="POST" onsubmit="return validateCloseForm(event)">
                    @csrf
                    <input type="hidden" name="ticket_id" id="modalTicketId">
                    
                    <div class="modal-header">
                    <h5 class="modal-title">Tutup Tiket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <p id="modalTicketTitle" class="mb-3"></p>
                    
                    <div class="mb-3">
                        <label for="resolution_notes" class="form-label">Catatan Penyelesaian <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('resolution_notes') is-invalid @enderror" 
                            id="resolution_notes" 
                            name="resolution_notes" 
                            rows="4"
                            required
                            placeholder="Jelaskan bagaimana masalah ini diselesaikan..."></textarea>
                        @error('resolution_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Pastikan sebelum menutup tiket:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmResolved" name="confirm_resolved" required>
                            <label class="form-check-label" for="confirmResolved">
                                Saya konfirmasi bahwa masalah telah benar-benar diselesaikan
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Tutup Tiket</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle modal data population
        const closeTicketModal = document.getElementById('closeTicketModal');
        if (closeTicketModal) {
            closeTicketModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const ticketId = button.getAttribute('data-ticket-id');
                const ticketTitle = button.getAttribute('data-ticket-title');
                
                // Set data in modal
                const modalTicketIdInput = document.getElementById('modalTicketId');
                if (modalTicketIdInput) {
                    modalTicketIdInput.value = ticketId;
                    console.log('Setting ticket ID:', ticketId);
                } else {
                    console.error('Could not find modalTicketId input');
                }
                
                document.getElementById('modalTicketTitle').textContent = 
                    `Anda akan menutup: ${ticketTitle}`;
            });

            // Reset form when modal is closed
            closeTicketModal.addEventListener('hidden.bs.modal', function() {
                const form = closeTicketModal.querySelector('form');
                form.reset();
                document.getElementById('modalTicketTitle').textContent = '';
            });
        }
    });
</script>
@endpush
@endsection