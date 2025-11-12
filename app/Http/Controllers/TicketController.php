<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\Sla;
use App\Models\User;
use App\Models\Outlet;
use Illuminate\Http\Request;
use App\Models\TicketComment;
use Yajra\DataTables\Facades\DataTables;

class TicketController extends Controller
{
    /**
     * Tampilkan daftar tiket (halaman admin).
     * Komentar: menampilkan semua tiket dengan paginate.
     */
    public function index(Request $request)
    {
        // Jika request dari DataTable (check both ajax() dan draw parameter)
        if ($request->ajax() || $request->has('draw')) {
            return $this->getTicketsDataTable($request);
        }

        $user = auth()->user();
        
        // For superadmin, provide a list of outlets for optional filtering
        $outlets = null;
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('superadmin')) {
            $outlets = Outlet::orderBy('name')->get();
        }

        return view('admin.tickets.index', compact('outlets'));
    }

    /**
     * Ambil data tiket untuk DataTable
     */
    private function getTicketsDataTable(Request $request)
    {
        $user = auth()->user();

        $query = Ticket::with(['ticketType', 'sla', 'createdBy', 'assignTo'])
            ->withCount('ratings')
            ->withAvg('ratings', 'rating');

        // If the current user is an admin (not superadmin) and has an outlet assigned,
        // only show tickets for that outlet. Superadmins can optionally filter by outlet.
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('admin') && ! $user->hasRole('superadmin') && ! empty($user->outlet_id)) {
            $query->where('outlet_id', $user->outlet_id);
        } else {
            // Superadmin: allow optional outlet filtering via query parameter
            if ($request->filled('outlet_id')) {
                $query->where('outlet_id', $request->get('outlet_id'));
            }
        }

        // Filter by status from DataTable search
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by priority from DataTable search
        if ($request->filled('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('ticket_id', function ($ticket) {
                return $ticket->id;
            })
            ->addColumn('title', function ($ticket) {
                return \Str::limit($ticket->title, 30);
            })
            ->addColumn('type', function ($ticket) {
                return $ticket->ticketType->name ?? '-';
            })
            ->addColumn('rating', function ($ticket) {
                $avg = $ticket->ratings_avg_rating ?? null;
                $count = $ticket->ratings_count ?? 0;
                if ($avg) {
                    return '<div class="text-warning">★ ' . number_format($avg, 1) . ' (' . $count . ')</div>';
                }
                return '-';
            })
            ->addColumn('status', function ($ticket) {
                $badge = $ticket->status === 'open' ? 'success' : ($ticket->status === 'closed' ? 'secondary' : 'warning');
                return '<span class="badge bg-' . $badge . '">' . $ticket->status . '</span>';
            })
            ->addColumn('priority', function ($ticket) {
                $badge = $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'info');
                return '<span class="badge bg-' . $badge . '">' . $ticket->priority . '</span>';
            })
            ->addColumn('created_by', function ($ticket) {
                return $ticket->createdBy->name ?? '-';
            })
            ->addColumn('pending_reason', function ($ticket) {
                if ($ticket->status === 'pending') {
                    return $ticket->pending_reason ?? '-';
                }
                return '-';
            })
            ->addColumn('pending_until', function ($ticket) {
                if ($ticket->status === 'pending' && $ticket->pending_until) {
                    return \Carbon\Carbon::parse($ticket->pending_until)->format('d/m/Y');
                }
                return '-';
            })
            ->addColumn('assign_to', function ($ticket) {
                if ($ticket->assignTo) {
                    return $ticket->assignTo->name;
                }
                return '<span class="badge bg-warning">Belum Ditugaskan</span>';
            })
            ->addColumn('created_at', function ($ticket) {
                return $ticket->created_at->format('d/m/Y H:i');
            })
            ->addColumn('actions', function ($ticket) {
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<a href="' . route('admin.tickets.show', $ticket) . '" class="btn btn-sm btn-info">Lihat</a>';
                
                if (!$ticket->assign_to && $ticket->status === 'open') {
                    $actions .= '<a href="' . route('admin.tickets.show', $ticket) . '#assign" class="btn btn-sm btn-warning">Tugaskan</a>';
                }
                
                $actions .= '<a href="' . route('admin.tickets.edit', $ticket) . '" class="btn btn-sm btn-secondary">Edit</a>';
                $actions .= '<form action="' . route('admin.tickets.destroy', $ticket) . '" method="POST" class="d-inline swal-delete" data-name="' . $ticket->id . '">';
                $actions .= csrf_field() . method_field('DELETE');
                $actions .= '<button class="btn btn-sm btn-danger">Hapus</button>';
                $actions .= '</form>';
                $actions .= '</div>';
                
                return $actions;
            })
            ->rawColumns(['rating', 'status', 'priority', 'assign_to', 'actions'])
            ->make(true);
    }

    /**
     * Tampilkan form membuat tiket baru.
     */
    public function create()
    {
        $types = TicketType::where('is_active', 1)->get();
        $slas = Sla::all();
        $users = User::orderBy('name')->get();
        // Users untuk pilihan created_by dan assign_to
        $users = User::orderBy('name')->get();

        $outlets = null;
        $user = auth()->user();
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('superadmin')) {
            $outlets = Outlet::orderBy('name')->get();
        }

        return view('admin.tickets.create', compact('types', 'slas', 'users', 'outlets'));
    }

    /**
     * Simpan tiket baru.
     */
    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated();
        
        // Set defaults
        $data['status'] = 'open';
        $data['priority'] = $data['priority'] ?? 'low';
        $data['created_by'] = $data['created_by'] ?? auth()->id();
        
        if (!$data['created_by']) {
            return redirect()->back()->with('error', 'Tidak dapat membuat tiket: User ID tidak valid.');
        }
        
        // If an admin (non-superadmin) creates the ticket, force the ticket's outlet to their outlet
        $user = auth()->user();
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('admin') && ! $user->hasRole('superadmin') && ! empty($user->outlet_id)) {
            $data['outlet_id'] = $user->outlet_id;
        }

        $ticket = Ticket::create($data);

        return redirect()->route('admin.tickets.index')->with('success', 'Tiket berhasil dibuat.');
    }

    /**
     * Tampilkan detail tiket beserta komentar.
     */
    public function show(Ticket $ticket)
    {
        $this->ensureOutletAccess($ticket);
        // muat relasi yang diperlukan termasuk komentar dan pengguna pembuat
        $ticket->load(['comments.user', 'ticketType', 'sla', 'createdBy', 'assignTo']);

        // Ambil daftar handler yang tersedia — hanya handler pada outlet tiket (jika ada)
        $handlersQuery = User::whereHas('role', function($query) {
            $query->where('name', 'handler');
        });

        if (!empty($ticket->outlet_id)) {
            $handlersQuery->where('outlet_id', $ticket->outlet_id);
        }

        $handlers = $handlersQuery->orderBy('name')->get();

    // Compute rating aggregates for admin view
    $ratingsCount = $ticket->ratings()->count();
    $ratingsAvg = $ratingsCount ? $ticket->ratings()->avg('rating') : null;

    return view('admin.tickets.show', compact('ticket', 'handlers', 'ratingsCount', 'ratingsAvg'));
    }

    /**
     * Tugaskan tiket ke handler.
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $this->ensureOutletAccess($ticket);
        $data = $request->validate([
            'handler_id' => ['required', 'exists:tbl_users,id'],
        ]);

        // Pastikan handler yang dipilih memiliki role handler
        $handler = User::whereHas('role', function($query) {
            $query->where('name', 'handler');
        })->findOrFail($data['handler_id']);

        $ticket->update([
            'assign_to' => $handler->id,
            'status' => 'in_progress'
        ]);

        // Buat komentar sistem untuk tracking
        $ticket->comments()->create([
            'user_id' => auth()->id(),
            'comment' => "Tiket ditugaskan kepada handler: {$handler->name}",
            'is_system_comment' => true
        ]);

        return redirect()
            ->route('admin.tickets.show', $ticket)
            ->with('success', "Tiket berhasil ditugaskan kepada {$handler->name}");
    }

    /**
     * Tangani penambahan komentar dari form di halaman show.
     * Komentar disimpan ke tabel ticket_comments.
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        $this->ensureOutletAccess($ticket);
        $data = $request->validate([
            'comment' => ['required', 'string', 'max:65535'],
        ]);

        // Pastikan user terautentikasi sebelum menambahkan komentar
        $userId = auth()->id();
        if (!$userId) {
            return redirect()->route('admin.tickets.show', $ticket)->with('error', 'Harap login terlebih dahulu untuk mengomentari.');
        }

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'comment' => $data['comment'],
        ]);

        return redirect()->route('admin.tickets.show', $ticket)->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit tiket.
     */
    public function edit(Ticket $ticket)
    {
        $this->ensureOutletAccess($ticket);
        $types = TicketType::where('is_active', 1)->get();
        $slas = Sla::all();
        $users = User::orderBy('name')->get();
        return view('admin.tickets.edit', compact('ticket', 'types', 'slas', 'users'));
    }

    /**
     * Update data tiket.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $this->ensureOutletAccess($ticket);
        $data = $request->validated();
        $ticket->update($data);
        return redirect()->route('admin.tickets.index')->with('success', 'Tiket berhasil diperbarui.');
    }

    /**
     * Admin action to close/clear pending state on a ticket.
     * Admin closing pending will behave like handler close: status -> in_progress if assigned, else open.
     */
    public function closePendingAsAdmin(Request $request, Ticket $ticket)
    {
        $this->ensureOutletAccess($ticket);

        try {
            $newStatus = $ticket->assign_to ? 'in_progress' : 'open';
            $ticket->update([
                'status' => $newStatus,
                'pending_reason' => null,
                'pending_until' => null,
            ]);

            $ticket->comments()->create([
                'user_id' => auth()->id(),
                'comment' => "Admin menghapus status PENDING. Mengubah status menjadi: {$newStatus}",
                'is_system_comment' => true,
            ]);

            return redirect()->back()->with('success', 'Pending berhasil dihapus oleh admin.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus pending.');
        }
    }

    /**
     * Hapus tiket.
     */
    public function destroy(Ticket $ticket)
    {
        $this->ensureOutletAccess($ticket);
        $ticket->delete();
        return redirect()->route('admin.tickets.index')->with('success', 'Tiket berhasil dihapus.');
    }

    /**
     * Ensure that admin users can only access tickets that belong to their outlet.
     */
    protected function ensureOutletAccess(Ticket $ticket)
    {
        $user = auth()->user();
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('admin') && ! $user->hasRole('superadmin') && !empty($user->outlet_id)) {
            if ((string)$ticket->outlet_id !== (string)$user->outlet_id) {
                abort(403, 'Unauthorized access to ticket for different outlet.');
            }
        }
    }
}
