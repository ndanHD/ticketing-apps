<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\Sla;
use App\Models\User;
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
        $tickets = Ticket::with(['ticketType', 'sla', 'createdBy'])
            ->withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.tickets.index', compact('tickets'));
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
        
        return view('admin.tickets.create', compact('types', 'slas', 'users'));
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
        
        $ticket = Ticket::create($data);

        return redirect()->route('admin.tickets.index')->with('success', 'Tiket berhasil dibuat.');
    }

    /**
     * Tampilkan detail tiket beserta komentar.
     */
    public function show(Ticket $ticket)
    {
        // muat relasi yang diperlukan termasuk komentar dan pengguna pembuat
        $ticket->load(['comments.user', 'ticketType', 'sla', 'createdBy', 'assignTo']);

        // Ambil daftar handler yang tersedia
        $handlers = User::whereHas('role', function($query) {
            $query->where('name', 'handler');
        })->orderBy('name')->get();

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
        $data = $request->validated();
        $ticket->update($data);
        return redirect()->route('admin.tickets.index')->with('success', 'Tiket berhasil diperbarui.');
    }

    /**
     * Hapus tiket.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('admin.tickets.index')->with('success', 'Tiket berhasil dihapus.');
    }
}
