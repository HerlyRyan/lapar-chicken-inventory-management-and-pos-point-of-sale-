<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Role::query();
        
        if (request('q')) {
            $q = request('q');
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('code', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }
        
        if (request('is_active') !== null) {
            $query->where('is_active', request('is_active'));
        }
        
        $roles = $query->withCount('users', 'permissions')
                      ->orderBy('name')
                      ->paginate(15)
                      ->withQueryString();
        
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::where('is_active', true)
                                ->orderBy('group')
                                ->orderBy('name')
                                ->get()
                                ->groupBy('group');
        
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {



        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:roles,code',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'code.required' => 'Kode role wajib diisi.',
            'code.unique' => 'Kode role sudah digunakan.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $role = Role::create($validated);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if ($role->isPrimarySuperAdmin()) {
            return redirect()->route('roles.index')->with('error', 'Role Super Admin tidak dapat diedit.');
        }
        $permissions = Permission::where('is_active', true)
                                ->orderBy('name')
                                ->get()
                                ->groupBy(function ($item) {
                                    // Group permissions by the part of their name before the first dot (e.g., 'users.create' -> 'users')
                                    return explode('.', $item->name)[0];
                                });
        
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if ($role->isPrimarySuperAdmin()) {
            return redirect()->route('roles.index')->with('error', 'Role Super Admin tidak dapat diedit.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'code' => 'required|string|max:50|unique:roles,code,' . $role->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah digunakan.',
            'code.required' => 'Kode role wajib diisi.',
            'code.unique' => 'Kode role sudah digunakan.',
            'permissions.*.exists' => 'Hak akses yang dipilih tidak valid.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $role->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active'],
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (!$role->deletable()) {
            return redirect()->route('roles.index')
                ->with('error', 'Role Super Admin utama tidak dapat dihapus.');
        }
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh user.');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}
