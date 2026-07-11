<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    public function index(): View
    {
        return view('products.brands.index');
    }

    public function Datatable(): JsonResponse
    {
        $brands = Brand::select('brands.*')->withCount('products');

        return DataTables::eloquent($brands)
            ->addColumn('status', fn (Brand $brand) => '<span class="badge badge-light-'.($brand->status ? 'success' : 'danger').'">'
                .($brand->status ? __('Active') : __('Inactive')).'</span>')
            ->addColumn('action', fn (Brand $brand) =>
                '<a href="'.route('brands.edit', $brand).'" class="btn btn-sm btn-icon btn-light-primary me-2" title="'.__('Edit').'"><i class="fas fa-pen"></i></a>'
                .'<form action="'.route('brands.destroy', $brand).'" method="POST" class="d-inline delete-form">'
                .csrf_field().method_field('DELETE')
                .'<button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="'.__('Delete').'"><i class="fas fa-trash"></i></button>'
                .'</form>')
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    public function create(): View
    {
        return view('products.brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Brand::create($validated);

        return redirect()->route('brands.index')->with('status', __('Brand created successfully.'));
    }

    public function edit(Brand $brand): View
    {
        return view('products.brands.edit', ['brand' => $brand]);
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
        ]);

        $brand->update([
            'name' => $validated['name'],
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('brands.index')->with('status', __('Brand updated successfully.'));
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->exists()) {
            return back()->with('error', __('This brand has products linked to it and cannot be deleted.'));
        }

        $brand->delete();

        return redirect()->route('brands.index')->with('status', __('Brand deleted.'));
    }
}
