@extends('template.layout')

@section('title', 'Tiket Saya')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tiket Saya</h2>
        <a href="{{ route('user.tickets.create') }}" class="btn btn-primary">
            Buat Tiket Baru
        </a>
    </div>

    @if($tickets->isEmpty())
        <div class="alert alert-info">
            Anda belum memiliki tiket. Klik tombol "Buat Tiket Baru" untuk membuat tiket.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipe</th>
                        <th>Subjek</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->id }}</td>
                            <td>{{ $ticket->ticketType->name ?? '-' }}</td>
                            <td>{{ $ticket->title }}</td>
                            <td>
                                <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'closed' ? 'secondary' : 'warning') }}">
                                    {{ $ticket->status }}
                                </span>
                            </td>
                            <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('user.tickets.show', $ticket) }}" class="btn btn-sm btn-info">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
@endsection