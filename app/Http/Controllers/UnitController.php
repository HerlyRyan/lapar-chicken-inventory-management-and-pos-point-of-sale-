<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Traits\TableFilterTrait;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    use TableFilterTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Unit::query();
        
        // Define searchable and sortable columns
        $searchableColumns = ['unit_name', 'abbreviation', 'description'];
        $sortableColumns = ['unit_name', 'abbreviation', 'created_at', 'updated_at'];
        
        // Apply the standard filtering, sorting, and pagination
        $units = $this->applyFilterSortPaginate(
            $query, 
            $searchableColumns, 
            $sortableColumns, 
            'unit_name', // default sort column
            'asc'       // default sort direction
        );
        
        // Get current sort parameters for the view
        $sortColumn = request('sort', 'unit_name');
        $sortDirection = request('direction', 'asc');
        
        return view('units.index', compact('units', 'sortColumn', 'sortDirection'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'unit_name' => 'required|string|max:255|unique:units,unit_name',
            'abbreviation' => 'required|string|max:10|unique:units,abbreviation',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ], [
            'unit_name.required' => 'Nama satuan wajib diisi.',
            'unit_name.unique' => 'Nama satuan sudah terdaftar. Silakan gunakan nama lain.',
            'abbreviation.required' => 'Singkatan wajib diisi.',
            'abbreviation.unique' => 'Singkatan sudah digunakan. Silakan gunakan singkatan lain.',
        ]);
        
        Unit::create([
            'unit_name' => $request->unit_name,
            'abbreviation' => $request->abbreviation,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);
        
        return redirect()->route('units.index')->with('success', 'Satuan berhasil ditambahkan.');
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
    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'unit_name' => 'required|string|max:255|unique:units,unit_name,' . $unit->id,
            'abbreviation' => 'required|string|max:10|unique:units,abbreviation,' . $unit->id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ], [
            'unit_name.required' => 'Nama satuan wajib diisi.',
            'unit_name.unique' => 'Nama satuan sudah terdaftar. Silakan gunakan nama lain.',
            'abbreviation.required' => 'Singkatan wajib diisi.',
            'abbreviation.unique' => 'Singkatan sudah digunakan. Silakan gunakan singkatan lain.',
        ]);
        
        $unit->update([
            'unit_name' => $request->unit_name,
            'abbreviation' => $request->abbreviation,
            'description' => $request->description,
            'is_active' => $request->is_active ?? false,
        ]);
        
        return redirect()->route('units.index')->with('success', 'Satuan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        // Check if unit is being used
        if ($unit->rawMaterials()->exists() || $unit->finishedProducts()->exists()) {
            return redirect()->route('units.index')
                ->with('error', 'Unit tidak dapat dihapus karena masih digunakan oleh bahan mentah atau produk.');
        }

        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Satuan berhasil dihapus.');
    }

    /**
     * Toggle the active status of a unit.
     */
    public function toggleStatus(Unit $unit)
    {
        $unit->update(['is_active' => !$unit->is_active]);

        $status = $unit->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('units.index')
                        ->with('success', "Satuan berhasil {$status}.");
    }
}
