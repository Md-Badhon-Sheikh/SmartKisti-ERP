<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SubCategoryController extends Controller
{
    /**
     * Display the index page.
     */
    public function Index()
    {
        return view('products.sub-categories.index', [
            'categories' => Category::where('status', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Return DataTable JSON for the listing.
     */
    public function Datatable(Request $request): JsonResponse
    {
        $query = SubCategory::select(['id', 'category_id', 'name', 'status', 'created_at'])
            ->with('category')
            ->withCount('products');

        return DataTables::of($query)
            ->addColumn('category_name', fn ($row) => $row->category->name)
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
            ->rawColumns(['category_name', 'products_count_badge', 'status', 'action'])
            ->make(true);
    }

    /**
     * Store a newly created Sub Category.
     */
    public function Store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id'         => 'required|exists:categories,id',
            'sub_category_name'   => 'required|string|max:255',
            'sub_category_status' => 'nullable|in:0,1',
        ]);

        $subCategory = SubCategory::create([
            'category_id' => $validated['category_id'],
            'name'        => $validated['sub_category_name'],
            'status'      => $validated['sub_category_status'] ?? 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sub category created successfully.',
            'data'    => $subCategory,
        ], 201);
    }

    /**
     * Show a single Sub Category (used by edit & view AJAX fetch).
     */
    public function Show(int $id): JsonResponse
    {
        $subCategory = SubCategory::with('category')->withCount('products')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'             => $subCategory->id,
                'category_id'    => $subCategory->category_id,
                'category_name'  => $subCategory->category->name,
                'name'           => $subCategory->name,
                'status'         => $subCategory->status,
                'products_count' => $subCategory->products_count,
                'created_at'     => $subCategory->created_at?->format('d M Y, h:i A'),
            ],
        ]);
    }

    /**
     * Update an existing Sub Category. (POST only)
     */
    public function Update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'category_id'         => 'required|exists:categories,id',
            'sub_category_name'   => 'required|string|max:255',
            'sub_category_status' => 'nullable|in:0,1',
        ]);

        $subCategory = SubCategory::findOrFail($id);
        $subCategory->update([
            'category_id' => $validated['category_id'],
            'name'        => $validated['sub_category_name'],
            'status'      => $validated['sub_category_status'] ?? $subCategory->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sub category updated successfully.',
            'data'    => $subCategory,
        ]);
    }

    /**
     * Delete a Sub Category. (POST only)
     */
    public function Delete(int $id): JsonResponse
    {
        $subCategory = SubCategory::findOrFail($id);

        if ($subCategory->products()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This sub category has products linked to it and cannot be deleted.',
            ], 422);
        }

        $subCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sub category deleted successfully.',
        ]);
    }

    /**
     * Toggle the status.
     */
    public function ToggleStatus(int $id): JsonResponse
    {
        $subCategory = SubCategory::findOrFail($id);
        $subCategory->update(['status' => !$subCategory->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'status'  => $subCategory->status,
        ]);
    }
}
