<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    /**
     * Display the index page.
     */
    public function Index()
    {
        return view('products.brands.index');
    }

    /**
     * Return DataTable JSON for the listing.
     */
    public function Datatable(Request $request): JsonResponse
    {
        $query = Brand::select(['id', 'name', 'status', 'created_at'])
            ->withCount('products');

        return DataTables::of($query)
            ->addColumn('products_count_badge', function ($row) {
                return '<span class="badge badge-light">' . $row->products_count . '</span>';
            })
            ->addColumn('status', function ($row) {
                $checked    = $row->status ? 'checked' : '';
                $badgeClass = $row->status ? 'badge-light-success' : 'badge-light-danger';
                $label      = $row->status ? __('Active') : __('Inactive');
                return '
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge ' . $badgeClass . '">' . $label . '</span>
                        <div class="form-check form-switch ms-2 mb-0">
                            <input class="form-check-input status-toggle" type="checkbox"
                                data-id="' . $row->id . '" ' . $checked . '>
                        </div>
                    </div>';
            })
            ->addColumn('action', function ($row) {
                $html = '<div class="d-flex justify-content-end gap-2">'
                    . '<button class="btn btn-light-info btn-view px-4 py-2" data-id="' . $row->id . '">'
                    . '<i class="fas fa-eye"></i>'
                    . '</button>';

                $html .= '<button class="btn btn-sm btn-light-primary btn-edit px-4 py-2" data-id="' . $row->id . '">'
                    . '<i class="fas fa-edit"></i>'
                    . '</button>';

                $html .= '<button class="btn btn-sm btn-light-danger btn-delete px-4 py-2" data-id="' . $row->id . '">'
                    . '<i class="fas fa-trash"></i>'
                    . '</button>';

                $html .= '</div>';

                return $html;
            })
            ->rawColumns(['products_count_badge', 'status', 'action'])
            ->make(true);
    }

    /**
     * Store a newly created Brand.
     */
    public function Store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'brand_name'   => 'required|string|max:255|unique:brands,name',
            'brand_status' => 'nullable|in:0,1',
        ]);

        $brand = Brand::create([
            'name'   => $validated['brand_name'],
            'status' => $validated['brand_status'] ?? 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Brand created successfully.',
            'data'    => $brand,
        ], 201);
    }

    /**
     * Show a single Brand (used by edit & view AJAX fetch).
     */
    public function Show(int $id): JsonResponse
    {
        $brand = Brand::withCount('products')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'             => $brand->id,
                'name'           => $brand->name,
                'status'         => $brand->status,
                'products_count' => $brand->products_count,
                'created_at'     => $brand->created_at?->format('d M Y, h:i A'),
            ],
        ]);
    }

    /**
     * Update an existing Brand. (POST only)
     */
    public function Update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'brand_name'   => 'required|string|max:255|unique:brands,name,' . $id,
            'brand_status' => 'nullable|in:0,1',
        ]);

        $brand = Brand::findOrFail($id);
        $brand->update([
            'name'   => $validated['brand_name'],
            'status' => $validated['brand_status'] ?? $brand->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Brand updated successfully.',
            'data'    => $brand,
        ]);
    }

    /**
     * Delete a Brand. (POST only)
     */
    public function Delete(int $id): JsonResponse
    {
        $brand = Brand::findOrFail($id);

        if ($brand->products()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This brand has products linked to it and cannot be deleted.',
            ], 422);
        }

        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully.',
        ]);
    }

    /**
     * Toggle the status.
     */
    public function ToggleStatus(int $id): JsonResponse
    {
        $brand = Brand::findOrFail($id);
        $brand->update(['status' => !$brand->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'status'  => $brand->status,
        ]);
    }
}
