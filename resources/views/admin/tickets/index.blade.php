@extends('admin.layout')

@section('title', 'Daftar Tiket')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Daftar Tiket</h1>
        <a href="{{ route('admin.tickets.create') }}" class="btn btn-primary">Buat Tiket</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Tipe</th>
                    <th>Rating</th>
                    <th>Status</th>
                    <th>Prioritas</th>
                    <th>Dibuat Oleh</th>
                    <th>Assign to</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->id }}</td>
                        <td>{{ Str::limit($ticket->title, 30) }}</td>
                        <td>{{ $ticket->ticketType->name ?? '-' }}</td>
                        <td>
                            @php
                                $avg = $ticket->ratings_avg_rating ?? null;
                                $count = $ticket->ratings_count ?? 0;
                            @endphp
                            @include('components.star-rating', ['rating' => $avg, 'count' => $count])
                        </td>
                        <td>
                            <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'closed' ? 'secondary' : 'warning') }}">
                                {{ $ticket->status }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'info') }}">
                                {{ $ticket->priority }}
                            </span>
                        </td>
                        <td>{{ $ticket->createdBy->name ?? '-' }}</td>
                        <td>
                            @if($ticket->assignTo)
                                {{ $ticket->assignTo->name }}
                            @else
                                <span class="badge bg-warning">Belum Ditugaskan</span>
                            @endif
                        </td>
                        <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-info">Lihat</a>
                                @if(!$ticket->assign_to && $ticket->status === 'open')
                                    <a href="{{ route('admin.tickets.show', $ticket) }}#assign" class="btn btn-sm btn-warning">Tugaskan</a>
                                @endif
                                <a href="{{ route('admin.tickets.edit', $ticket) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST" class="d-inline swal-delete" data-name="{{ $ticket->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">Tidak ada tiket.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $tickets->links() }}
@endsection
