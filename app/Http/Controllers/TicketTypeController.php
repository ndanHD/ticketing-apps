<?php

namespace App\Http\Controllers;

use App\Models\TicketType;
use Illuminate\Http\Request;

class TicketTypeController extends Controller
{
    /**
     * Menampilkan daftar tipe tiket.
     */
    public function index()
    {
        $types = TicketType::orderBy('name')->paginate(10);
        return view('admin.ticket-types.index', compact('types'));
    }

    /**
     * Menampilkan form untuk membuat tipe tiket baru.
     */
    public function create()
    {
        return view('admin.ticket-types.create');
    }

    /**
     * Menyimpan tipe tiket baru ke database.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        // Set default is_active jika tidak ada
        $data['is_active'] = $data['is_active'] ?? true;

        TicketType::create($data);

        return redirect()
            ->route('admin.ticket-types.index')
            ->with('success', 'Tipe tiket berhasil ditambahkan');
    }

    /**
     * Menampilkan form untuk mengedit tipe tiket.
     */
    public function edit(TicketType $ticketType)
    {
        return view('admin.ticket-types.edit', compact('ticketType'));
    }

    /**
     * Mengupdate data tipe tiket ke database.
     */
    public function update(Request $request, TicketType $ticketType)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        // Set default is_active jika tidak ada
        $data['is_active'] = $data['is_active'] ?? true;

        $ticketType->update($data);

        return redirect()
            ->route('admin.ticket-types.index')
            ->with('success', 'Tipe tiket berhasil diperbarui');
    }

    /**
     * Menghapus tipe tiket dari database.
     */
    public function destroy(TicketType $ticketType)
    {
        // Cek apakah tipe tiket sedang digunakan
        $ticketCount = $ticketType->tickets()->count();
        if ($ticketCount > 0) {
            return redirect()
                ->route('admin.ticket-types.index')
                ->with('error', "Tipe tiket tidak dapat dihapus karena sedang digunakan di {$ticketCount} tiket");
        }

        $ticketType->delete();

        return redirect()
            ->route('admin.ticket-types.index')
            ->with('success', 'Tipe tiket berhasil dihapus');
    }
}