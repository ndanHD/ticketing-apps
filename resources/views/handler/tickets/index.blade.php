@extends('admin.layout')

@section('title', 'Tiket Saya (Handler)')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Tiket yang Ditugaskan</h1>
        @if(isset($newCount) && $newCount > 0)
            <span class="badge bg-success">{{ $newCount }} Baru</span>
        @endif
    </div>

    @if($tickets->isEmpty())
        <div class="alert alert-info">Tidak ada tiket yang ditugaskan ke Anda.</div>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipe</th>
                        
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->id }}</td>
                            <td>{{ $ticket->ticketType->name ?? '-' }}</td>
                            
                            <td>{{ $ticket->status }}</td>
                            <td>{{ $ticket->createdBy->name ?? '-' }}</td>
                            <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('handler.tickets.show', $ticket) }}" class="btn btn-sm btn-info">Lihat</a>
                                    @if($ticket->status !== 'closed')
                                        <button type="button" class="btn btn-sm btn-success" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#closeTicketModal"
                                            data-ticket-id="{{ $ticket->id }}"
                                            data-ticket-title="{{ $ticket->title ?? 'Tiket #' . $ticket->id }}">
                                            Tutup Tiket
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $tickets->links() }}
    @endif

    <!-- Modal Tutup Tiket -->
    <div class="modal fade" id="closeTicketModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('handler.tickets.close') }}" method="POST" onsubmit="return validateCloseForm(event)">
                    @csrf
                    <input type="hidden" name="ticket_id" id="modalTicketId" value="{{ $ticket->id }}">
                    
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

            function validateCloseForm(event) {
                const ticketId = document.getElementById('modalTicketId').value;
                const resolutionNotes = document.getElementById('resolution_notes').value;
                const confirmed = document.getElementById('confirmResolved').checked;

                console.log('Form submission:', {
                    ticketId,
                    resolutionNotes,
                    confirmed
                });

                if (!ticketId) {
                    alert('Error: No ticket ID found. Please try again.');
                    event.preventDefault();
                    return false;
                }
                return true;
            }
        });
    </script>
    @endpush
@endsection