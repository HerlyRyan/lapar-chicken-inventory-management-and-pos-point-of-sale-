<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Permission::query();
        
        if (request('q')) {
            $q = request('q');
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('code', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }
        
        if (request('group')) {
            $query->where('group', request('group'));
        }
        
        if (request('is_active') !== null) {
            $query->where('is_active', request('is_active'));
        }
        
        $permissions = $query->orderBy('group')->orderBy('name')->paginate(15)->withQueryString();
        $groups = Permission::distinct()->pluck('group')->filter();
        
        return view('permissions.index', compact('permissions', 'groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100|unique:permissions,code',
            'group' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Nama hak akses wajib diisi.',
            'name.max' => 'Nama hak akses maksimal 100 karakter.',
            'code.required' => 'Kode hak akses wajib diisi.',
            'code.unique' => 'Kode hak akses sudah digunakan.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
        ]);
        
        Permission::create($request->only('name', 'code', 'group', 'description', 'is_active'));
        return redirect()->route('permissions.index')->with('success', 'Hak akses berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100|unique:permissions,code,' . $permission->id,
            'group' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Nama hak akses wajib diisi.',
            'name.max' => 'Nama hak akses maksimal 100 karakter.',
            'code.required' => 'Kode hak akses wajib diisi.',
            'code.unique' => 'Kode hak akses sudah digunakan.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
        ]);
        
        $permission->update($request->only('name', 'code', 'group', 'description', 'is_active'));
        return redirect()->route('permissions.index')->with('success', 'Hak akses berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Hak akses berhasil dihapus.');
    }
}
