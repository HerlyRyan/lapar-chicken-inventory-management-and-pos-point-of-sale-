<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        $query = Category::with(['finishedProducts', 'rawMaterials', 'semiFinishedProducts']);

        // for column selection
        $columns = [
            ['key' => 'code', 'label' => 'Kode'],
            ['key' => 'name', 'label' => 'Nama Kategori'],
            ['key' => 'description', 'label' => 'Deskripsi'],
            ['key' => 'is_active', 'label' => 'Status'],
        ];

        // Search global
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($status = $request->get('is_active')) {
            $query->where('is_active', $status);
        }

        // Sorting
        if ($sortBy = $request->get('sort_by')) {
            $sortDir = $request->get('sort_dir', 'asc');
            $query->orderBy($sortBy, $sortDir);
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $categories */
        $categories = $query->paginate(10);

        $statuses = [
            1 => 'Aktif',
            0 => 'Nonaktif',
        ];

        // Array untuk komponen filter
        $selects = [
            [
                'name' => 'is_active',
                'label' => 'Semua Status',
                'options' => $statuses,
            ],
        ];

        if ($request->ajax()) {
            return response()->json([
                'data' => $categories->items(),
                'links' => (string) $categories->links('vendor.pagination.tailwind'),
            ]);
        }

        $categories = $query->latest()->paginate(15);

        return view('categories.index', [
            'categories' => $categories->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $categories, // tetap simpan pagination untuk tampilkan links
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'code' => 'required|string|max:50|unique:categories',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ], [
            'name.unique' => 'Nama kategori sudah digunakan',
            'code.unique' => 'Kode kategori sudah digunakan'
        ]);

        $category = Category::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $category->load(['finishedProducts', 'rawMaterials', 'semiFinishedProducts']);

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'code' => 'required|string|max:50|unique:categories,code,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ], [
            'name.unique' => 'Nama kategori sudah digunakan',
            'code.unique' => 'Kode kategori sudah digunakan'
        ]);

        $category->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        // Check if category has finished products
        if ($category->finishedProducts()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    /**
     * Toggle the active status of a category.
     */
    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('categories.index')
            ->with('success', "Kategori berhasil {$status}.");
    }

    /**
     * Get categories for API calls (for dropdowns, etc.)
     */
    public function getActiveCategories()
    {
        $categories = Category::active()
            ->select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }
}
