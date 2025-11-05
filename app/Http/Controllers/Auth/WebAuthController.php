<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Users;
use App\Models\Role;

class WebAuthController extends Controller
{
    /**
     * Menampilkan form login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login untuk user (session-based)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required']
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect based on user role
            if ($user->role) {
                switch ($user->role->name) {
                    case 'admin':
                        return redirect()->intended(route('admin.dashboard'));
                    case 'handler':
                        return redirect()->intended(route('handler.tickets.index'));
                    default:
                        return redirect()->intended(route('user.tickets.index'));
                }
            }
            
            // Default redirect if no role is set
            return redirect()->intended(route('user.tickets.index'));
        }

        return back()->withErrors(['email' => 'Email atau password salah'])->onlyInput('email');
    }

    /**
     * Menampilkan form register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi (session-based). Membuat user baru di tabel tbl_users.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255'],
            'password' => ['required','string','min:8','confirmed']
        ]);

        // Get the default user role
        $defaultRole = Role::where('name', 'user')->first();
        if (!$defaultRole) {
            $defaultRole = Role::create(['name' => 'user', 'can_handle_ticket' => false]);
        }

        $user = Users::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
            'role_id' => $defaultRole->id
        ]);

        Auth::login($user);
        return redirect()->route('admin.dashboard')->with('success', 'Registrasi berhasil. Selamat datang!');
    }

    /**
     * Logout user (session)
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Anda berhasil logout');
    }
}
