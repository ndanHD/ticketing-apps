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
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        // Prepare datasets for charts
        $statusCounts = \App\Models\Ticket::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $outletCounts = \App\Models\Ticket::select('tbl_outlets.name as outlet', \Illuminate\Support\Facades\DB::raw('count(tbl_tickets.id) as count'))
            ->leftJoin('tbl_outlets', 'tbl_tickets.outlet_id', '=', 'tbl_outlets.id')
            ->groupBy('tbl_outlets.name')
            ->orderByDesc('count')
            ->get()
            ->mapWithKeys(function($r){ return [$r->outlet ?? 'Unassigned' => (int)$r->count]; })
            ->toArray();

        return view('admin.dashboard', compact('statusCounts', 'outletCounts'));
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
    public function usersIndex(Request $request)
    {
        // Jika request dari DataTable (check both ajax() dan draw parameter)
        if ($request->ajax() || $request->has('draw')) {
            return $this->getUsersDataTable($request);
        }

        return view('admin.users.index');
    }

    /**
     * Ambil data user untuk DataTable
     */
    private function getUsersDataTable(Request $request)
    {
        $query = User::with('role')->where('role_id', '!=', null);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('id_short', function ($user) {
                return substr($user->id, 0, 8);
            })
            ->addColumn('name', function ($user) {
                return $user->name;
            })
            ->addColumn('email', function ($user) {
                return $user->email;
            })
            ->addColumn('job_title', function ($user) {
                return $user->job_tittle ?? '-';
            })
            ->addColumn('role', function ($user) {
                return $user->role->name ?? '-';
            })
            ->addColumn('created_at', function ($user) {
                return $user->created_at->format('d/m/Y H:i');
            })
            ->addColumn('status', function ($user) {
                $badge = $user->is_active ? 'success' : 'danger';
                $text = $user->is_active ? 'Active' : 'Non Active';
                return '<span class="badge bg-' . $badge . '">' . $text . '</span>';
            })
            ->addColumn('actions', function ($user) {
                if ($user->role && $user->role->name === 'superadmin') {
                    return '<span class="badge bg-secondary">Superadmin</span>';
                }

                $actions = '<div class="btn-group" role="group">';
                $actions .= '<a href="' . route('admin.users.edit', $user) . '" class="btn btn-sm btn-info"><i class="bi bi-pencil"></i> Edit</a>';
                $actions .= '<form action="' . route('admin.users.destroy', $user) . '" method="POST" class="d-inline swal-delete" data-name="' . $user->name . '">';
                $actions .= csrf_field() . method_field('DELETE');
                $actions .= '<button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Hapus</button>';
                $actions .= '</form>';
                $actions .= '</div>';

                return $actions;
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
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
