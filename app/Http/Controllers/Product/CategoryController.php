<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('products.categories.index');
    }

    public function Datatable(): JsonResponse
    {
        $categories = Category::select('categories.*')->withCount('products');

        return DataTables::eloquent($categories)
            ->addColumn('brand_required', fn (Category $category) => '<span class="badge badge-light-'.($category->brand_required ? 'primary' : 'secondary').'">'
                .($category->brand_required ? __('Yes') : __('No')).'</span>')
            ->addColumn('status', fn (Category $category) => '<span class="badge badge-light-'.($category->status ? 'success' : 'danger').'">'
                .($category->status ? __('Active') : __('Inactive')).'</span>')
            ->addColumn('action', fn (Category $category) =>
                '<a href="'.route('categories.edit', $category).'" class="btn btn-sm btn-icon btn-light-primary me-2" title="'.__('Edit').'"><i class="fas fa-pen"></i></a>'
                .'<form action="'.route('categories.destroy', $category).'" method="POST" class="d-inline delete-form">'
                .csrf_field().method_field('DELETE')
                .'<button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="'.__('Delete').'"><i class="fas fa-trash"></i></button>'
                .'</form>')
            ->rawColumns(['brand_required', 'status', 'action'])
            ->toJson();
    }

    public function create(): View
    {
        return view('products.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand_required' => ['sometimes', 'boolean'],
        ]);

        Category::create([
            'name' => $validated['name'],
            'brand_required' => $request->boolean('brand_required'),
        ]);

        return redirect()->route('categories.index')->with('status', __('Category created successfully.'));
    }

    public function edit(Category $category): View
    {
        return view('products.categories.edit', ['category' => $category]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand_required' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'boolean'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'brand_required' => $request->boolean('brand_required'),
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('categories.index')->with('status', __('Category updated successfully.'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists() || $category->subCategories()->exists()) {
            return back()->with('error', __('This category has sub-categories or products linked to it and cannot be deleted.'));
        }

        $category->delete();

        return redirect()->route('categories.index')->with('status', __('Category deleted.'));
    }
}
