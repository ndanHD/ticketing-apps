@extends('template.layout')

@section('title', 'Detail Tiket #' . $ticket->id)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Detail Tiket #{{ $ticket->id }}</h2>
                    <a href="{{ route('user.tickets.index') }}" class="btn btn-outline-secondary">
                        Kembali ke Daftar
                    </a>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ $ticket->tittle }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Status:</strong>
                                    <span
                                        class="badge bg-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'closed' ? 'secondary' : 'warning') }}">
                                        {{ $ticket->status }}
                                    </span>
                                </p>
                                <p><strong>Tipe:</strong> {{ $ticket->ticketType->name ?? '-' }}</p>
                                <p><strong>SLA:</strong> {{ $ticket->sla->name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p><strong>Dibuat:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                                @if ($ticket->assignTo)
                                    <p><strong>Ditangani oleh:</strong> {{ $ticket->assignTo->name }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="ticket-detail border-top pt-3">
                            <h6>Detail Masalah:</h6>
                            <textarea id="ticket-description" readonly>{{ $ticket->description }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Rating Form untuk Tiket yang Sudah Ditutup -->
                @if ($ticket->status === 'closed' && !$hasRated)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Beri Rating</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.tickets.rating.submit', $ticket) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Rating</label>
                                    <div class="rating-stars mb-2">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="rating"
                                                    id="rating{{ $i }}" value="{{ $i }}" required>
                                                <label class="form-check-label" for="rating{{ $i }}">
                                                    {{ $i }} ⭐
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                    @error('rating')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="rating-comment" class="form-label">Komentar</label>
                                    <textarea class="form-control @error('comment') is-invalid @enderror" id="rating-comment" name="comment" rows="3"
                                        required placeholder="Berikan feedback Anda tentang penanganan tiket ini..."></textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Kirim Rating</button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Komentar -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Diskusi</h5>
                    </div>
                    <div class="card-body">
                        @if ($ticket->status !== 'closed')
                            <form action="{{ route('user.tickets.comments.store', $ticket) }}" method="POST"
                                class="mb-4">
                                @csrf
                                <div class="mb-3">
                                    <label for="comment" class="form-label">Tambah Komentar</label>
                                    <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" rows="3"
                                        placeholder="Tulis komentar atau pertanyaan tambahan..."></textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Kirim</button>
                            </form>
                        @endif

                        <div class="comments-list">
                            @forelse($ticket->comments as $comment)
                                <div
                                    class="comment-item mb-3 p-3 border rounded {{ $comment->user->id === auth()->id() ? 'bg-light' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong>{{ $comment->user->name }}</strong>
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
