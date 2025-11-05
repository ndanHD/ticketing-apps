<?php

namespace App\Http\Controllers;

use App\Models\Sla;
use Illuminate\Http\Request;

class SlaController extends Controller
{
    /**
     * Menampilkan daftar SLA (Service Level Agreement).
     */
    public function index()
    {
        $slas = Sla::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.slas.index', compact('slas'));
    }

    /**
     * Menampilkan form untuk membuat SLA baru.
     */
    public function create()
    {
        return view('admin.slas.create');
    }

    /**
     * Menyimpan SLA baru ke database.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'response_time' => ['required', 'integer', 'min:1'],
            'resolution_time' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Sla::create($data);

        return redirect()
            ->route('admin.slas.index')
            ->with('success', 'SLA berhasil ditambahkan');
    }

    /**
     * Menampilkan form untuk mengedit SLA.
     */
    public function edit(Sla $sla)
    {
        return view('admin.slas.edit', compact('sla'));
    }

    /**
     * Mengupdate data SLA ke database.
     */
    public function update(Request $request, Sla $sla)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'response_time' => ['required', 'integer', 'min:1'],
            'resolution_time' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $sla->update($data);

        return redirect()
            ->route('admin.slas.index')
            ->with('success', 'SLA berhasil diperbarui');
    }

    /**
     * Menghapus SLA dari database.
     */
    public function destroy(Sla $sla)
    {
        // Cek apakah SLA sedang digunakan di tiket
        $ticketCount = $sla->tickets()->count();
        if ($ticketCount > 0) {
            return redirect()
                ->route('admin.slas.index')
                ->with('error', "SLA tidak dapat dihapus karena sedang digunakan di {$ticketCount} tiket");
        }

        $sla->delete();

        return redirect()
            ->route('admin.slas.index')
            ->with('success', 'SLA berhasil dihapus');
    }
}