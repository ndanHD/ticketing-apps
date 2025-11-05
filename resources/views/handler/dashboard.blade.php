@extends('layouts.handler')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1><i class="bi bi-speedometer2"></i> Handler Dashboard</h1>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ Auth::user()->ticketsAssigned()->count() }}</h3>
                        <div>Assigned Tickets</div>
                    </div>
                    <div>
                        <i class="bi bi-ticket fs-1"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="{{ route('handler.tickets.index') }}">View Details</a>
                <div class="small text-white"><i class="bi bi-chevron-right"></i></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ Auth::user()->ticketsAssigned()->where('status', 'resolved')->count() }}</h3>
                        <div>Resolved Tickets</div>
                    </div>
                    <div>
                        <i class="bi bi-check-circle fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-warning text-dark mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ Auth::user()->ticketsAssigned()->where('status', 'open')->count() }}</h3>
                        <div>Open Tickets</div>
                    </div>
                    <div>
                        <i class="bi bi-exclamation-circle fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Assigned Tickets</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Created</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(Auth::user()->ticketsAssigned()->latest()->take(10)->get() as $ticket)
                            <tr>
                                <td>{{ $ticket->id }}</td>
                                <td>{{ $ticket->title }}</td>
                                <td>
                                    @if($ticket->status == 'open')
                                        <span class="badge bg-warning">Open</span>
                                    @elseif($ticket->status == 'in_progress')
                                        <span class="badge bg-info">In Progress</span>
                                    @elseif($ticket->status == 'resolved')
                                        <span class="badge bg-success">Resolved</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $ticket->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->priority == 'high')
                                        <span class="badge bg-danger">High</span>
                                    @elseif($ticket->priority == 'medium')
                                        <span class="badge bg-warning text-dark">Medium</span>
                                    @else
                                        <span class="badge bg-info">Low</span>
                                    @endif
                                </td>
                                <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('handler.tickets.show', $ticket->id) }}" class="btn btn-sm btn-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection