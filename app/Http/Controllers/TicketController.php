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

class TicketController extends Controller
{
    /**
     * Tampilkan daftar tiket (halaman admin).
     * Komentar: menampilkan semua tiket dengan paginate.
     */
    public function index()
    {
        $user = auth()->user();
        $selectedOutlet = null;

        $query = Ticket::with(['ticketType', 'sla', 'createdBy'])
            ->withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->orderBy('created_at', 'desc');

        // If the current user is an admin (not superadmin) and has an outlet assigned,
        // only show tickets for that outlet. Superadmins can optionally filter by outlet.
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('admin') && ! $user->hasRole('superadmin') && ! empty($user->outlet_id)) {
            $query->where('outlet_id', $user->outlet_id);
            $selectedOutlet = $user->outlet;
        } else {
            // Superadmin: allow optional outlet filtering via query parameter
            if (request()->filled('outlet_id')) {
                $query->where('outlet_id', request()->get('outlet_id'));
                $selectedOutlet = Outlet::find(request()->get('outlet_id'));
            }
        }

        $tickets = $query->paginate(20);
        // For superadmin, provide a list of outlets for optional filtering
        $outlets = null;
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('superadmin')) {
            $outlets = Outlet::orderBy('name')->get();
        }

        return view('admin.tickets.index', compact('tickets', 'outlets', 'selectedOutlet'));
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
