<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\Sla;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketUserController extends Controller
{
    /**
     * Tampilkan daftar tiket pengguna.
     * Hanya menampilkan tiket yang dibuat oleh user yang sedang login.
     */
    public function index()
    {
        $tickets = Ticket::with(['ticketType', 'sla'])
            ->where('created_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('user.tickets.index', compact('tickets'));
    }

    /**
     * Tampilkan form pembuatan tiket baru untuk user.
     */
    public function create()
    {
        $types = TicketType::where('is_active', 1)->get();
        $slas = Sla::all();
        
        return view('user.tickets.create', compact('types', 'slas'));
    }

    /**
     * Simpan tiket baru dari user.
     * Secara otomatis set created_by ke ID user yang login.
     */
    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated();
        
        $ticket = Ticket::create(array_merge($data, [
            'created_by' => auth()->id(),
            'status' => 'open'
        ]));

        return redirect()
            ->route('user.tickets.show', $ticket)
            ->with('success', 'Tiket berhasil dibuat. Tim kami akan segera merespon.');
    }

    /**
     * Tampilkan detail tiket beserta riwayat komentar.
     * User hanya bisa melihat tiket yang dia buat.
     */
    public function show(Ticket $ticket)
    {
        $userId = auth()->id();
        if ($ticket->created_by !== $userId) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        $ticket->load(['comments.user', 'ticketType', 'sla', 'createdBy', 'assignTo', 'ratings']);
        
        // Cek apakah user sudah memberikan rating
        $hasRated = $ticket->ratings()->where('user_id', $userId)->exists();
        
        return view('user.tickets.show', compact('ticket', 'hasRated'));
    }

    /**
     * Submit rating untuk tiket yang sudah selesai.
     */
    public function submitRating(Request $request, Ticket $ticket)
    {
        $userId = auth()->id();
        if (!$userId) {
            return redirect()->route('login');
        }

        // Validasi akses
        if ($ticket->created_by !== $userId) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        // Validasi status tiket
        if ($ticket->status !== 'closed') {
            return redirect()->back()->with('error', 'Rating hanya bisa diberikan untuk tiket yang sudah ditutup.');
        }

        // Validasi input
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:65535'],
        ]);

        // Begin transaction
        DB::beginTransaction();
        try {
            // Simpan rating
            $ticket->ratings()->create([
                'user_id' => $userId,
                'target_user_id' => $ticket->assign_to,
                'rating' => $data['rating']
            ]);

            // Simpan komentar rating
            $ticket->comments()->create([
                'user_id' => $userId,
                'comment' => $data['comment'],
                'is_rating_comment' => true
            ]);

            DB::commit();
            return redirect()
                ->route('user.tickets.show', $ticket)
                ->with('success', 'Terima kasih atas feedback Anda!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan rating.');
        }
    }

    /**
     * Tambah komentar baru pada tiket.
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        // Pastikan user hanya bisa komen di tiketnya sendiri
        $userId = auth()->id();
        if (!$userId) {
            return redirect()->route('login');
        }
        if ($ticket->created_by !== $userId) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        $data = $request->validate([
            'comment' => ['required', 'string', 'max:65535'],
        ]);

        $ticket->comments()->create([
            'user_id' => $userId,
            'comment' => $data['comment'],
        ]);

        return redirect()
            ->route('user.tickets.show', $ticket)
            ->with('success', 'Komentar berhasil ditambahkan.');
    }
}