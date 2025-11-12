<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

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

        // Chart data: status breakdown for assigned tickets
        $statusCounts = Ticket::where('assign_to', $user->id)
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('handler.dashboard', compact(
            'totalTickets', 
            'resolvedTickets', 
            'openTickets', 
            'recentTickets',
            'statusCounts'
        ));
    }

    /**
     * Menampilkan tiket yang ditugaskan ke handler yang sedang login
     */
    public function index(Request $request)
    {
        // Jika request dari DataTable (check both ajax() dan draw parameter)
        if ($request->ajax() || $request->has('draw')) {
            return $this->getHandlerTicketsDataTable($request);
        }

        $user = auth()->user();

        // Hitung tiket baru dalam 24 jam terakhir sebagai indikator notifikasi
        $newCount = Ticket::where('assign_to', $user->id)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        return view('handler.tickets.index', compact('newCount'));
    }

    /**
     * Ambil data tiket handler untuk DataTable
     */
    private function getHandlerTicketsDataTable(Request $request)
    {
        $user = auth()->user();

        // Build query for tickets assigned to this handler
        $query = Ticket::where('assign_to', $user->id)
            ->with(['ticketType', 'createdBy']);

        // Filter by status from DataTable
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        return DataTables::of($query->orderBy('created_at', 'desc'))
            ->addIndexColumn()
            ->addColumn('ticket_id', function ($ticket) {
                return $ticket->id;
            })
            ->addColumn('type', function ($ticket) {
                return $ticket->ticketType->name ?? '-';
            })
            ->addColumn('status', function ($ticket) {
                $badge = $ticket->status === 'open' ? 'success' : ($ticket->status === 'closed' ? 'secondary' : 'warning');
                return '<span class="badge bg-' . $badge . '">' . $ticket->status . '</span>';
            })
            ->addColumn('created_by', function ($ticket) {
                return $ticket->createdBy->name ?? '-';
            })
            ->addColumn('created_at', function ($ticket) {
                return $ticket->created_at->format('Y-m-d H:i');
            })
            ->addColumn('actions', function ($ticket) {
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<a href="' . route('handler.tickets.show', $ticket) . '" class="btn btn-sm btn-info">Lihat</a>';
                
                if ($ticket->status !== 'closed') {
                    $actions .= '<button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#closeTicketModal" data-ticket-id="' . $ticket->id . '" data-ticket-title="' . ($ticket->title ?? 'Tiket #' . $ticket->id) . '">Tutup Tiket</button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
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

    /**
     * Set ticket to pending by handler assigned to it.
     */
    public function setPending(Request $request, Ticket $ticket)
    {
        // Ensure the handler is assigned to this ticket
        if ($ticket->assign_to !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        $data = $request->validate([
            'pending_reason' => ['required', 'string', 'max:65535'],
            'pending_until' => ['required', 'date', 'after_or_equal:today'],
        ]);

        DB::beginTransaction();
        try {
            $ticket->update([
                'status' => 'pending',
                'pending_reason' => $data['pending_reason'],
                'pending_until' => $data['pending_until'],
            ]);

            // Add system comment
            $ticket->comments()->create([
                'user_id' => auth()->id(),
                'comment' => "Menandai tiket sebagai PENDING: {$data['pending_reason']} (sampai {$data['pending_until']})",
                'is_system_comment' => true,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Tiket berhasil di-set sebagai pending.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal set pending tiket.');
        }
    }

    /**
     * Close/Clear pending state (handler action).
     * If ticket has an assigned handler, set status to in_progress; otherwise set to open.
     */
    public function closePending(Request $request, Ticket $ticket)
    {
        if ($ticket->assign_to !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        DB::beginTransaction();
        try {
            $newStatus = $ticket->assign_to ? 'in_progress' : 'open';
            $ticket->update([
                'status' => $newStatus,
                'pending_reason' => null,
                'pending_until' => null,
            ]);

            $ticket->comments()->create([
                'user_id' => auth()->id(),
                'comment' => "Menghapus status PENDING. Mengubah status menjadi: {$newStatus}",
                'is_system_comment' => true,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Pending berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus pending.');
        }
    }
}
