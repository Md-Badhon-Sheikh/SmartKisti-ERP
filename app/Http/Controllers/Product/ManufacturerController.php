<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ManufacturerController extends Controller
{
    public function index(): View
    {
        return view('products.manufacturers.index');
    }

    public function Datatable(): JsonResponse
    {
        $manufacturers = Manufacturer::select('manufacturers.*')->withCount('products');

        return DataTables::eloquent($manufacturers)
            ->addColumn('type', fn (Manufacturer $manufacturer) => __(ucwords(str_replace('_', ' ', $manufacturer->type))))
            ->addColumn('status', fn (Manufacturer $manufacturer) => '<span class="badge badge-light-'.($manufacturer->status ? 'success' : 'danger').'">'
                .($manufacturer->status ? __('Active') : __('Inactive')).'</span>')
            ->addColumn('action', fn (Manufacturer $manufacturer) =>
                '<a href="'.route('manufacturers.edit', $manufacturer).'" class="btn btn-sm btn-icon btn-light-primary me-2" title="'.__('Edit').'"><i class="fas fa-pen"></i></a>'
                .'<form action="'.route('manufacturers.destroy', $manufacturer).'" method="POST" class="d-inline delete-form">'
                .csrf_field().method_field('DELETE')
                .'<button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="'.__('Delete').'"><i class="fas fa-trash"></i></button>'
                .'</form>')
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    public function create(): View
    {
        return view('products.manufacturers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Manufacturer::TYPES)],
        ]);

        Manufacturer::create($validated);

        return redirect()->route('manufacturers.index')->with('status', __('Manufacturer created successfully.'));
    }

    public function edit(Manufacturer $manufacturer): View
    {
        return view('products.manufacturers.edit', ['manufacturer' => $manufacturer]);
    }

    public function update(Request $request, Manufacturer $manufacturer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Manufacturer::TYPES)],
            'status' => ['sometimes', 'boolean'],
        ]);

        $manufacturer->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('manufacturers.index')->with('status', __('Manufacturer updated successfully.'));
    }

    public function destroy(Manufacturer $manufacturer): RedirectResponse
    {
        if ($manufacturer->products()->exists()) {
            return back()->with('error', __('This manufacturer has products linked to it and cannot be deleted.'));
        }

        $manufacturer->delete();

        return redirect()->route('manufacturers.index')->with('status', __('Manufacturer deleted.'));
    }
}
