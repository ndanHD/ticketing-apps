<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketRatingController extends Controller
{
    /**
     * Minimal stub to accept rating store requests for API route reflection and basic tests.
     */
    public function store(Request $request, $ticket)
    {
        // For now, respond with 201 Created. Real implementation exists elsewhere in the app.
        return response()->json(['message' => 'rating stored (stub)'], 201);
    }
}
