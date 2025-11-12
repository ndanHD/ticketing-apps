<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Users;
use App\Models\Role;
use Carbon\Carbon;

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
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->must_change_password) {
                return redirect()->route('password.change')->with('warning', 'You must change your password.');
            }

            return $this->redirectToRoleDashboard($user);
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.',]);
    }

    /**
     * Redirect to dashboard based on role
     */
    private function redirectToRoleDashboard($user)
    {
        return match ($user->role->name ?? null) {
            'admin', 'superadmin' => redirect()->intended(route('admin.dashboard')),
            'handler' => redirect()->intended(route('handler.dashboard')),
            'user' => redirect()->intended(route('user.dashboard')),
        };
    }


    /**
     * Menampilkan form ganti password
     */
    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    /**
     * Process password change
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if (!$user->must_change_password) {
            $rules['current_password'] = ['required'];
        }

        $request->validate($rules);

        if (!$user->must_change_password && !Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
            'last_change_password' => Carbon::now()->toDateTimeString()
        ]);

        return $this->redirectToRoleDashboard($user)->with('success', 'Password changed successfully.');
    }


    /**
     * Menampilkan form register
     */
    public function showRegister()
    {
        $outlets = \App\Models\Outlet::orderBy('name')->get();
        return view('auth.register', compact('outlets'));
    }

    /**
     * Proses registrasi (session-based). Membuat user baru di tabel tbl_users.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:tbl_users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'outlet_id' => ['nullable', 'exists:tbl_outlets,id'],
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
            'role_id' => $defaultRole->id,
            'outlet_id' => $data['outlet_id'] ?? null,
            'job_tittle' => 'Customer'
        ]);

        Auth::login($user);
        return redirect()->route('user.dashboard')->with('success', 'Registrasi berhasil. Selamat datang!');
    }

    /**
     * Logout user (session)
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Anda berhasil logout');
    }
}
