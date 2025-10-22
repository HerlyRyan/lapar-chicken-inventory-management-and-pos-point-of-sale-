<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Permission;
use App\Traits\TableFilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Contracts\View\View
     */
    use TableFilterTrait;

    public function index(Request $request)
    {
        $query = User::with(['branch', 'roles']);

        $columns = [
            ['key' => 'name', 'label' => 'Nama'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'branch', 'label' => 'Cabang'],
            ['key' => 'roles', 'label' => 'Role'],
            ['key' => 'is_active', 'label' => 'Status'],
        ];

        // === SEARCH ===
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('branch', fn($b) => $b->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('roles', fn($r) => $r->where('name', 'like', "%{$search}%"));
            });
        }

        // === FILTER STATUS ===
        if ($status = $request->get('is_active')) {
            $query->where('is_active', $status);
        }

        // === SORTING ===
        if ($sortBy = $request->get('sort_by')) {
            $sortDir = $request->get('sort_dir', 'asc');

            switch ($sortBy) {
                case 'branch':
                    // gunakan LEFT JOIN supaya null branch tetap muncul
                    $query->leftJoin('branches', 'branches.id', '=', 'users.branch_id')
                        ->orderBy('branches.name', $sortDir)
                        ->select('users.*');
                    break;

                case 'roles':
                    // LEFT JOIN pivot dan roles supaya user tanpa role tetap muncul
                    $query->leftJoin('user_roles', 'user_roles.user_id', '=', 'users.id')
                        ->leftJoin('roles', 'roles.id', '=', 'user_roles.role_id')
                        ->select('users.*')
                        ->groupBy('users.id')
                        ->orderByRaw("COALESCE(MIN(roles.name), '') $sortDir");
                    break;

                default:
                    $query->orderBy($sortBy, $sortDir);
            }
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $users */
        $users = $query->paginate(10);

        // === FILTER OPTIONS ===
        $branches = Branch::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();
        $roles = Role::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();

        $statuses = [
            1 => 'Aktif',
            0 => 'Nonaktif',
        ];

        $selects = [
            ['name' => 'branch_id', 'label' => 'Semua Cabang', 'options' => $branches],
            ['name' => 'role_id', 'label' => 'Semua Role', 'options' => $roles],
            ['name' => 'is_active', 'label' => 'Semua Status', 'options' => $statuses],
        ];

        if ($request->ajax()) {
            return response()->json([
                'data' => $users->items(),
                'links' => (string) $users->links('vendor.pagination.tailwind'),
            ]);
        }

        return view('users.index', [
            'users' => $users->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $users,
        ]);
    }


    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        $roles = Role::orderBy('name')->get();
        return view('users.create', compact('branches', 'roles'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Prepare phone number validation - ensure it starts with 62
        $phone = $request->phone;
        if ($phone && !str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        $request->merge(['phone' => $phone]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|regex:/^62\d{8,13}$/',
            'password' => 'required|string|min:8|confirmed',
            'branch_id' => 'nullable|exists:branches,id',
            'role_id' => 'required|exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ], [
            'phone.regex' => 'Format nomor telepon harus diawali dengan 62 dan diikuti 8-13 digit angka tanpa spasi atau tanda plus.',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'branch_id' => $validated['branch_id'] ?? null,
            'is_active' => $request->has('is_active') ? true : false,
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = 'avatar_' . time() . '.' . $avatar->getClientOriginalExtension();
            $path = $avatar->storeAs('avatars', $filename, 'public');
            $userData['avatar'] = $path;
        }

        $user = User::create($userData);

        // Assign role to user
        $user->roles()->attach($request->role_id);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Contracts\View\View
     */
    public function show(User $user)
    {
        $user->load(['branch', 'roles.permissions']);
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($item) {
            return explode(' ', $item->name)[0]; // Group by first word of permission name
        });
        return view('users.show', compact('user', 'permissions'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(User $user)
    {
        $branches = Branch::where('is_active', true)->get();
        $roles = Role::orderBy('name')->get();
        return view('users.edit', compact('user', 'branches', 'roles'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Prepare phone number validation - ensure it starts with 62
        $phone = $request->phone;
        if ($phone && !str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        $request->merge(['phone' => $phone]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'required|string|regex:/^62\d{8,13}$/',
            'password' => 'nullable|string|min:8|confirmed',
            'branch_id' => 'nullable|exists:branches,id',
            'role_id' => 'required|exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ], [
            'phone.regex' => 'Format nomor telepon harus diawali dengan 62 dan diikuti 8-13 digit angka tanpa spasi atau tanda plus.',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
            'branch_id' => $validated['branch_id'] ?? $user->branch_id,
            'is_active' => $request->has('is_active') ? true : false,
        ];

        // Update password only if provided
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatar = $request->file('avatar');
            $filename = 'avatar_' . time() . '.' . $avatar->getClientOriginalExtension();
            $path = $avatar->storeAs('avatars', $filename, 'public');
            $userData['avatar'] = $path;
        }

        $user->update($userData);

        // Update user role
        $user->roles()->sync([$request->role_id]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Don't allow deletion of primary super admin
        if ($user->isPrimarySuperAdmin()) {
            return redirect()->route('users.index')->with('error', 'Cannot delete the primary Super Admin user.');
        }

        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
