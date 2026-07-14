<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Display the index page.
     */
    public function Index()
    {
        return view('products.categories.index');
    }

    /**
     * Return DataTable JSON for the listing.
     */
    public function Datatable(Request $request): JsonResponse
    {
        $query = Category::select(['id', 'name', 'brand_required', 'status', 'created_at'])
            ->withCount('products');

        return DataTables::of($query)
            ->addColumn('products_count_badge', function ($row) {
                return '<span class="badge badge-light">' . $row->products_count . '</span>';
            })
            ->addColumn('brand_required_badge', function ($row) {
                return $row->brand_required
                    ? '<span class="badge badge-light-primary">' . __('Yes') . '</span>'
                    : '<span class="badge badge-light-secondary">' . __('No') . '</span>';
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
            ->rawColumns(['products_count_badge', 'brand_required_badge', 'status', 'action'])
            ->make(true);
    }

    /**
     * Store a newly created Category.
     */
    public function Store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_name'   => 'required|string|max:255|unique:categories,name',
            'brand_required'  => 'nullable|in:0,1',
            'category_status' => 'nullable|in:0,1',
        ]);

        $category = Category::create([
            'name'           => $validated['category_name'],
            'brand_required' => $validated['brand_required'] ?? 0,
            'status'         => $validated['category_status'] ?? 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data'    => $category,
        ], 201);
    }

    /**
     * Show a single Category (used by edit & view AJAX fetch).
     */
    public function Show(int $id): JsonResponse
    {
        $category = Category::withCount('products')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'             => $category->id,
                'name'           => $category->name,
                'brand_required' => $category->brand_required,
                'status'         => $category->status,
                'products_count' => $category->products_count,
                'created_at'     => $category->created_at?->format('d M Y, h:i A'),
            ],
        ]);
    }

    /**
     * Update an existing Category. (POST only)
     */
    public function Update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'category_name'   => 'required|string|max:255|unique:categories,name,' . $id,
            'brand_required'  => 'nullable|in:0,1',
            'category_status' => 'nullable|in:0,1',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name'           => $validated['category_name'],
            'brand_required' => $validated['brand_required'] ?? $category->brand_required,
            'status'         => $validated['category_status'] ?? $category->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data'    => $category,
        ]);
    }

    /**
     * Delete a Category. (POST only)
     */
    public function Delete(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        if ($category->products()->exists() || $category->subCategories()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This category has sub-categories or products linked to it and cannot be deleted.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }

    /**
     * Toggle the status.
     */
    public function ToggleStatus(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->update(['status' => !$category->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'status'  => $category->status,
        ]);
    }
}