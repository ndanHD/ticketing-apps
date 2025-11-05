<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\Users;
use Illuminate\Support\Facades\DB;

class HandlerController extends Controller
{
    /**
     * Menampilkan dashboard handler
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get ticket statistics
        // Get ticket statistics
        $totalTickets = Ticket::where('assign_to', $user->id)->count();
        $resolvedTickets = Ticket::where('assign_to', $user->id)->where('status', 'resolved')->count();
        $openTickets = Ticket::where('assign_to', $user->id)->whereIn('status', ['open', 'in_progress'])->count();
        
        // Get recent tickets
        $recentTickets = Ticket::where('assign_to', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('handler.dashboard', compact(
            'totalTickets', 
            'resolvedTickets', 
            'openTickets', 
            'recentTickets'
        ));
    }

    /**
     * Menampilkan tiket yang ditugaskan ke handler yang sedang login
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil semua tiket yang ditugaskan ke user ini
        $tickets = Ticket::where('assign_to', $user->id)
            ->with(['ticketType', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Hitung tiket baru dalam 24 jam terakhir sebagai indikator notifikasi
        $newCount = Ticket::where('assign_to', $user->id)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        return view('handler.tickets.index', compact('tickets', 'newCount'));
    }

    /**
     * Menampilkan detail tiket
     */
    public function show(Request $request, Ticket $ticket)
    {
        // Pastikan handler hanya bisa melihat tiket yang ditugaskan kepadanya
        if ($ticket->assign_to !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        return view('handler.tickets.show', compact('ticket'));
    }

    /**
     * Menambahkan komentar pada tiket
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        // Validasi bahwa handler memiliki akses ke tiket ini
        if ($ticket->assign_to !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        $data = $request->validate([
            'comment' => ['required', 'string', 'min:3']
        ]);

        $ticket->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $data['comment']
        ]);

        return redirect()
            ->back()
            ->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Menutup tiket yang telah selesai ditangani.
     */
    public function closeTicket(Request $request)
    {
        $data = $request->validate([
            // The Ticket model uses table `tbl_tickets`, so the exists rule must check that table
            'ticket_id' => ['required', 'string', 'exists:tbl_tickets,id'],
            'resolution_notes' => ['required', 'string', 'min:10'],
            'confirm_resolved' => ['required', 'accepted']
        ]);

        $user = auth()->user();
        $ticket = Ticket::where('id', $data['ticket_id'])
            ->where('assign_to', $user->id)
            ->firstOrFail();

        // Begin transaction
        DB::beginTransaction();
        try {
            // Update ticket status
            $ticket->update([
                'status' => 'closed',
                'closed_at' => now()
            ]);

            // Add resolution comment
            $ticket->comments()->create([
                'user_id' => $user->id,
                'comment' => $data['resolution_notes'],
                'is_resolution_note' => true
            ]);

            DB::commit();
            return redirect()
                ->back()
                ->with('success', 'Tiket berhasil ditutup. Terima kasih atas penanganannya!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menutup tiket. Silakan coba lagi.');
        }
    }
}
