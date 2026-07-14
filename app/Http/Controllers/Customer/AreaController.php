<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AreaController extends Controller
{
    /**
     * Display the index page.
     */
    public function Index()
    {
        return view('customers.areas.index');
    }

    /**
     * Return DataTable JSON for the listing.
     */
    public function Datatable(Request $request): JsonResponse
    {
        $query = Area::select(['id', 'name', 'status', 'created_at'])
            ->withCount('customers');

        return DataTables::of($query)
            ->addColumn('customers_count_badge', function ($row) {
                return '<span class="badge badge-light">' . $row->customers_count . '</span>';
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
            ->rawColumns(['customers_count_badge', 'status', 'action'])
            ->make(true);
    }

    /**
     * Store a newly created Area.
     */
    public function Store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'area_name'   => 'required|string|max:255|unique:areas,name',
            'area_status' => 'nullable|in:0,1',
        ]);

        $area = Area::create([
            'name'   => $validated['area_name'],
            'status' => $validated['area_status'] ?? 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Area created successfully.',
            'data'    => $area,
        ], 201);
    }

    /**
     * Show a single Area (used by edit & view AJAX fetch).
     */
    public function Show(int $id): JsonResponse
    {
        $area = Area::withCount('customers')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'              => $area->id,
                'name'            => $area->name,
                'status'          => $area->status,
                'customers_count' => $area->customers_count,
                'created_at'      => $area->created_at?->format('d M Y, h:i A'),
            ],
        ]);
    }

    /**
     * Update an existing Area. (POST only)
     */
    public function Update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'area_name'   => 'required|string|max:255|unique:areas,name,' . $id,
            'area_status' => 'nullable|in:0,1',
        ]);

        $area = Area::findOrFail($id);
        $area->update([
            'name'   => $validated['area_name'],
            'status' => $validated['area_status'] ?? $area->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Area updated successfully.',
            'data'    => $area,
        ]);
    }

    /**
     * Delete an Area. (POST only)
     */
    public function Delete(int $id): JsonResponse
    {
        $area = Area::findOrFail($id);

        if ($area->customers()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This area has customers linked to it and cannot be deleted.',
            ], 422);
        }

        $area->delete();

        return response()->json([
            'success' => true,
            'message' => 'Area deleted successfully.',
        ]);
    }

    /**
     * Toggle the status.
     */
    public function ToggleStatus(int $id): JsonResponse
    {
        $area = Area::findOrFail($id);
        $area->update(['status' => !$area->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'status'  => $area->status,
        ]);
    }
}
