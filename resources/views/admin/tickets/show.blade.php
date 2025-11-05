@extends('admin.layout')

@section('title', 'Detail Tiket')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Detail Tiket #{{ $ticket->id }}</h1>
        <div>
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary">Kembali</a>
            <a href="{{ route('admin.tickets.edit', $ticket) }}" class="btn btn-secondary">Edit</a>
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
                        <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'closed' ? 'secondary' : 'warning') }}">
                            {{ $ticket->status }}
                        </span>
                    </p>
                </div>
                <div class="col-md-4">
                    <p><strong>Created By:</strong> {{ $ticket->createdBy->name ?? '-' }}</p>
                    <p><strong>Tanggal:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                    <p>
                        <strong>Prioritas:</strong>
                        <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'info') }}">
                            {{ $ticket->priority }}
                        </span>
                    </p>
                    <p>
                        <strong>Rating:</strong>
                        @if(isset($ratingsCount) && $ratingsCount > 0)
                            @include('components.star-rating', ['rating' => $ratingsAvg, 'count' => $ratingsCount])
                        @else
                            <span class="text-muted">Belum ada rating</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($ticket->status === 'open' && !$ticket->assign_to)
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
                            <select name="handler_id" class="form-select @error('handler_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Handler --</option>
                                @foreach($handlers as $handler)
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

            <div class="mt-4">
                <h6>Detail Masalah:</h6>
                <div class="border rounded p-3 bg-light">
                    {!! nl2br(e($ticket->description)) !!}
                </div>
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
