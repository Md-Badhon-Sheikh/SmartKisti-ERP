<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SubCategoryController extends Controller
{
    public function index(): View
    {
        return view('products.sub-categories.index');
    }

    public function Datatable(): JsonResponse
    {
        $subCategories = SubCategory::select('sub_categories.*')->with('category')->withCount('products');

        return DataTables::eloquent($subCategories)
            ->addColumn('category', fn (SubCategory $subCategory) => $subCategory->category->name)
            ->addColumn('status', fn (SubCategory $subCategory) => '<span class="badge badge-light-'.($subCategory->status ? 'success' : 'danger').'">'
                .($subCategory->status ? __('Active') : __('Inactive')).'</span>')
            ->addColumn('action', fn (SubCategory $subCategory) =>
                '<a href="'.route('sub-categories.edit', $subCategory).'" class="btn btn-sm btn-icon btn-light-primary me-2" title="'.__('Edit').'"><i class="fas fa-pen"></i></a>'
                .'<form action="'.route('sub-categories.destroy', $subCategory).'" method="POST" class="d-inline delete-form">'
                .csrf_field().method_field('DELETE')
                .'<button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="'.__('Delete').'"><i class="fas fa-trash"></i></button>'
                .'</form>')
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    public function create(): View
    {
        return view('products.sub-categories.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        SubCategory::create($validated);

        return redirect()->route('sub-categories.index')->with('status', __('Sub category created successfully.'));
    }

    public function edit(SubCategory $subCategory): View
    {
        return view('products.sub-categories.edit', [
            'subCategory' => $subCategory,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, SubCategory $subCategory): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
        ]);

        $subCategory->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('sub-categories.index')->with('status', __('Sub category updated successfully.'));
    }

    public function destroy(SubCategory $subCategory): RedirectResponse
    {
        if ($subCategory->products()->exists()) {
            return back()->with('error', __('This sub category has products linked to it and cannot be deleted.'));
        }

        $subCategory->delete();

        return redirect()->route('sub-categories.index')->with('status', __('Sub category deleted.'));
    }
}
