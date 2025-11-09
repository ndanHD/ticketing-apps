<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Outlet;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\Sla;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $outlets = Outlet::all();
        return view('user.tickets.create', compact('types', 'slas', 'outlets'));
    }

    /**
     * Simpan tiket baru dari user.
     * Secara otomatis set created_by ke ID user yang login.
     */
    public function store(StoreTicketRequest $request)
    {
        $request->validate([
            'ticket_type_id' => 'required|exists:tbl_ticket_types,id',
            'sla_id'         => 'required|exists:tbl_slas,id',
            'outlet_id'      => 'required|exists:tbl_outlets,id',
            'title'          => 'required|string|max:100',
            'description'    => 'required|string',
        ]);

        $description = $request->description;

        // Ambil semua gambar tmp
        preg_match_all('/<img.*?src="([^"]+)"/', $description, $matches);
        $imageUrls = $matches[1] ?? [];

        // dd($description);
        foreach ($imageUrls as $url) {
            $fileName = basename($url);

            $tmpPath = 'tmp/' . $fileName;
            $mediaPath = 'media/' . $fileName;

            if (\Storage::disk('public')->exists($tmpPath)) {
                // Pindahkan dari tmp ke media
                \Storage::disk('public')->move($tmpPath, $mediaPath);

                // Update URL di description
                $description = str_replace('/storage/' . $tmpPath, '/storage/' . $mediaPath, $description);
            }
        }
        $ticket = Ticket::create([
            'ticket_type_id' => $request->ticket_type_id,
            'sla_id'         => $request->sla_id,
            'outlet_id'      => $request->outlet_id,
            'title'          => $request->title,
            'description'    => $description,
            'created_by'     => Auth()->id(),

        ]);
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
