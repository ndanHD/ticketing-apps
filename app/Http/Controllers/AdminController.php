<?php

namespace App\Http\Controllers;

use App\Mail\UserPasswordMail;
use App\Models\Outlet;
use App\Models\Users as User;
use App\Models\Role;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Get available roles based on logged-in user's role
     */
    protected function getAvailableRoles()
    {
        $user = auth()->user();

        $roleName = $user->role?->name ?? null;

        if ($roleName === 'superadmin') {
            // Superadmin can assign user, handler, and admin roles
            return Role::whereIn('name', ['user', 'handler', 'admin'])->get();
        } else {
            // Admin can only assign user and handler roles
            return Role::whereIn('name', ['user', 'handler'])->get();
        }
    }

    /**
     * Display list of users
     */
    public function usersIndex()
    {
        $users = User::with('role')->orderBy('id', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user creation form
     */
    public function usersCreate()
    {
        $outlets = Outlet::all();
        $availableRoles = $this->getAvailableRoles();
        return view('admin.users.create', [
            'availableRoles' => $availableRoles,
            'outlets' => $outlets,
        ]);
    }

    /**
     * Store new user
     */
    public function usersStore(Request $request)
    {
        // Get available roles for validation
        $availableRoles = $this->getAvailableRoles();
        $availableRoleIds = $availableRoles->pluck('id')->toArray();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:tbl_users,email'],
            // 'password' => ['required', 'confirmed', 'min:6'],
            'role_id' => ['required', Rule::in($availableRoleIds)],
            'outlet_id' => ['required', 'exists:tbl_outlets,id'],
            'job_tittle' => ['required', 'string', 'max:25'],
        ]);

        $randomPassword = Str::random(10);

        $user = Users::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($randomPassword),
            'role_id' => $data['role_id'],
            'outlet_id' => $data['outlet_id'],
            'job_tittle' => $data['job_tittle'],
        ]);
        Mail::to($user->email)->send(new UserPasswordMail($user, $randomPassword, 'new'));
        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show user edit form
     */
    public function usersEdit(Users $user)
    {
        // Prevent editing superadmin users
        $outlets = Outlet::all();
        $roleName = $user->role?->name ?? null;
        if ($roleName === 'superadmin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Akun superadmin tidak dapat diedit.');
        }

        $availableRoles = $this->getAvailableRoles();
        return view('admin.users.edit', compact('user', 'availableRoles', 'outlets'));
    }

    /**
     * Update existing user
     */
    public function usersUpdate(Request $request, Users $user)
    {
        // Double-check protection against editing superadmin
        $roleName = $user->role?->name ?? null;
        if ($roleName === 'superadmin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Akun superadmin tidak dapat diedit.');
        }

        // Get available roles for validation
        $availableRoles = $this->getAvailableRoles();
        $availableRoleIds = $availableRoles->pluck('id')->toArray();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('tbl_users')->ignore($user->id)],
            'role_id' => ['required', Rule::in($availableRoleIds)],
            'outlet_id' => ['required', 'exists:tbl_outlets,id'],
            'job_tittle' => ['required', 'string', 'max:25'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role_id = $data['role_id'];
        $user->outlet_id = $data['outlet_id'];
        $user->job_tittle = $data['job_tittle'];
        $user->is_active = $request->has('is_active');
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * reset password user
     */
    public function resetUserPassword(Users $user)
    {
        $temporaryPassword = Str::random(10);

        $user->password = Hash::make($temporaryPassword);
        $user->must_change_password = true;
        $user->last_change_password = Carbon::now()->toDateTimeString();
        $user->save();

        Mail::to($user->email)->send(new UserPasswordMail($user, $temporaryPassword, 'reset'));
        return back()->with('success', 'Password has been reset and emailed to the user.');
    }
    /**
     * Delete user
     */
    public function usersDestroy(User $user)
    {
        // Prevent deleting superadmin users
        $roleName = $user->role?->name ?? null;
        if ($roleName === 'superadmin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Akun superadmin tidak dapat dihapus.');
        }

        // Additional safety: prevent deleting the last superadmin
        $superadminCount = User::whereHas('role', function ($query) {
            $query->where('name', 'superadmin');
        })->count();

        if ($superadminCount <= 1 && $roleName === 'superadmin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat menghapus superadmin terakhir.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
