<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outlet;

class OutletController extends Controller
{
    // List outlets (superadmin)
    public function index()
    {
        $outlets = Outlet::orderBy('name')->paginate(20);
        return view('admin.outlets.index', compact('outlets'));
    }

    // Show create form
    public function create()
    {
        return view('admin.outlets.create');
    }

    // Store new outlet
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:512']
        ]);

        Outlet::create([
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
        ]);

        return redirect()->route('admin.outlets.index')->with('success', 'Outlet berhasil ditambahkan.');
    }
}
