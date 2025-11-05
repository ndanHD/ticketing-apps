<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
//     public function __construct()
//    {
//        $this->middleware('auth');
//    }
   /**
    * Tampilkan dashboard user dengan statistik tiket.
    */
   public function index()
   {
       $userId = auth()->id();
       
       $stats = [
           'total_tickets' => \App\Models\Ticket::where('created_by', $userId)->count(),
           'open_tickets' => \App\Models\Ticket::where('created_by', $userId)
               ->where('status', 'open')
               ->count(),
           'process_tickets' => \App\Models\Ticket::where('created_by', $userId)
               ->where('status', 'process')
               ->count(),
           'closed_tickets' => \App\Models\Ticket::where('created_by', $userId)
               ->where('status', 'closed')
               ->count(),
           'recent_tickets' => \App\Models\Ticket::where('created_by', $userId)
               ->with(['ticketType', 'sla'])
               ->orderBy('created_at', 'desc')
               ->take(5)
               ->get()
       ];
       
       return view('user.dashboard', compact('stats'));
   }
}
