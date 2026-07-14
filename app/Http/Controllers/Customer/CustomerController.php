<?php

namespace App\Http\Controllers\Customer;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\CustomerGuarantor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    /**
     * Display the index page.
     */
    public function Index()
    {
        return view('customers.index');
    }

    /**
     * Return DataTable JSON for the listing.
     */
    public function Datatable(Request $request): JsonResponse
    {
        $query = Customer::with('area')->select('customers.*');

        return DataTables::eloquent($query)
            ->addColumn('area', fn (Customer $customer) => $customer->area->name)
            ->addColumn('gender', function (Customer $customer) {
                return $customer->gender ? ucfirst($customer->gender) : '—';
            })
            ->addColumn('status', function (Customer $customer) {
                $checked    = $customer->status ? 'checked' : '';
                $badgeClass = $customer->status ? 'badge-light-success' : 'badge-light-danger';
                $label      = $customer->status ? __('Active') : __('Inactive');
                return '
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge ' . $badgeClass . '">' . $label . '</span>
                        <div class="form-check form-switch ms-2 mb-0">
                            <input class="form-check-input status-toggle" type="checkbox"
                                data-id="' . $customer->id . '" ' . $checked . '>
                        </div>
                    </div>';
            })
            ->addColumn('action', function (Customer $customer) use ($request) {
                $html = '<div class="d-flex justify-content-end gap-2">'
                    . '<button class="btn btn-light-info btn-view px-4 py-2" data-id="' . $customer->id . '">'
                    . '<i class="fas fa-eye"></i>'
                    . '</button>';

                if ($request->user()->hasAnyRole(['super-admin', 'admin', 'manager'])) {
                    $html .= '<a href="' . route('customers.edit', $customer->id) . '" class="btn btn-sm btn-light-primary px-4 py-2">'
                        . '<i class="fas fa-edit"></i>'
                        . '</a>';

                    $html .= '<button class="btn btn-sm btn-light-danger btn-delete px-4 py-2" data-id="' . $customer->id . '">'
                        . '<i class="fas fa-trash"></i>'
                        . '</button>';
                }

                $html .= '</div>';

                return $html;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Display the create page.
     */
    public function Create(): View
    {
        return view('customers.create', $this->formData());
    }

    /**
     * Store a newly created Customer.
     */
    public function Store(Request $request): RedirectResponse
    {
        $validated = $this->validateCustomer($request);

        $validated['customer_code'] = $this->generateCustomerCode();
        $validated['status']        = true;
        $validated['created_by']    = $request->user()->id;
        $validated['updated_by']    = $request->user()->id;

        if ($request->hasFile('photo')) {
            $validated['photo'] = Helper::upload('customer_photo_' . time() . '_' . uniqid(), $request->file('photo'), 'uploads/customers')['image_path'];
        }

        if ($request->hasFile('nid_image')) {
            $validated['nid_image'] = Helper::upload('customer_nid_' . time() . '_' . uniqid(), $request->file('nid_image'), 'uploads/customers')['image_path'];
        }

        $customer = Customer::create($validated);

        $this->storeDocuments($request, $customer);
        $this->syncGuarantors($request, $customer);

        return redirect()->route('customers.index')->with('status', __('Customer created successfully.'));
    }

    /**
     * Display the edit page.
     */
    public function Edit(Customer $customer): View
    {
        $customer->load(['documents', 'guarantors']);

        return view('customers.edit', array_merge(['customer' => $customer], $this->formData()));
    }

    /**
     * Show a single Customer (used by the view modal's AJAX fetch).
     */
    public function Show(int $id): JsonResponse
    {
        $customer = Customer::with(['area', 'documents', 'guarantors', 'createdBy', 'updatedBy'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                 => $customer->id,
                'customer_code'      => $customer->customer_code,
                'name'               => $customer->name,
                'description'        => $customer->description,
                'mobile'             => $customer->mobile,
                'alternative_mobile' => $customer->alternative_mobile,
                'nid'                => $customer->nid,
                'gender'             => $customer->gender,
                'father_name'        => $customer->father_name,
                'occupation'         => $customer->occupation,
                'area_id'            => $customer->area_id,
                'area_name'          => $customer->area->name,
                'address'            => $customer->address,
                'photo_url'          => $customer->photo ? asset($customer->photo) : null,
                'nid_image_url'      => $customer->nid_image ? asset($customer->nid_image) : null,
                'status'             => $customer->status,
                'documents'          => $customer->documents->map(fn (CustomerDocument $document) => [
                    'id'   => $document->id,
                    'name' => $document->file_name,
                    'url'  => asset($document->file_path),
                ]),
                'guarantors' => $customer->guarantors->map(fn (CustomerGuarantor $guarantor) => [
                    'id'        => $guarantor->id,
                    'name'      => $guarantor->name,
                    'relation'  => $guarantor->relation,
                    'mobile'    => $guarantor->mobile,
                    'nid'       => $guarantor->nid,
                    'address'   => $guarantor->address,
                    'photo_url' => $guarantor->photo ? asset($guarantor->photo) : null,
                ]),
                'created_by' => $customer->createdBy?->name,
                'updated_by' => $customer->updatedBy?->name,
                'created_at' => $customer->created_at?->format('d M Y, h:i A'),
            ],
        ]);
    }

    /**
     * Update an existing Customer.
     */
    public function Update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $this->validateCustomer($request, $customer);

        $validated['status']     = $request->boolean('status');
        $validated['updated_by'] = $request->user()->id;

        if ($request->hasFile('photo')) {
            if ($customer->photo) {
                File::delete(public_path($customer->photo));
            }
            $validated['photo'] = Helper::upload('customer_photo_' . time() . '_' . uniqid(), $request->file('photo'), 'uploads/customers')['image_path'];
        } elseif ($request->boolean('remove_photo')) {
            if ($customer->photo) {
                File::delete(public_path($customer->photo));
            }
            $validated['photo'] = null;
        }

        if ($request->hasFile('nid_image')) {
            if ($customer->nid_image) {
                File::delete(public_path($customer->nid_image));
            }
            $validated['nid_image'] = Helper::upload('customer_nid_' . time() . '_' . uniqid(), $request->file('nid_image'), 'uploads/customers')['image_path'];
        } elseif ($request->boolean('remove_nid_image')) {
            if ($customer->nid_image) {
                File::delete(public_path($customer->nid_image));
            }
            $validated['nid_image'] = null;
        }

        $customer->update($validated);

        $this->storeDocuments($request, $customer);
        $this->syncGuarantors($request, $customer);

        return redirect()->route('customers.index')->with('status', __('Customer updated successfully.'));
    }

    /**
     * Delete a Customer. (POST only)
     */
    public function Delete(int $id): JsonResponse
    {
        $customer = Customer::with(['documents', 'guarantors'])->findOrFail($id);

        if ($customer->photo) {
            File::delete(public_path($customer->photo));
        }

        if ($customer->nid_image) {
            File::delete(public_path($customer->nid_image));
        }

        foreach ($customer->documents as $document) {
            File::delete(public_path($document->file_path));
        }

        foreach ($customer->guarantors as $guarantor) {
            if ($guarantor->photo) {
                File::delete(public_path($guarantor->photo));
            }
        }

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully.',
        ]);
    }

    /**
     * Toggle the status.
     */
    public function ToggleStatus(int $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $customer->update(['status' => !$customer->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'status'  => $customer->status,
        ]);
    }

    /**
     * Delete a single document belonging to a Customer. (POST only)
     */
    public function DeleteDocument(int $id, int $documentId): JsonResponse
    {
        $document = CustomerDocument::where('customer_id', $id)->findOrFail($documentId);

        File::delete(public_path($document->file_path));
        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully.',
        ]);
    }

    /**
     * Upload and attach any submitted documents to the given Customer.
     */
    protected function storeDocuments(Request $request, Customer $customer): void
    {
        if (! $request->hasFile('other_documents')) {
            return;
        }

        foreach ($request->file('other_documents') as $file) {
            $stored = $this->storeRawFile($file, 'uploads/customers/documents');

            CustomerDocument::create([
                'customer_id' => $customer->id,
                'file_path'   => $stored['file_path'],
                'file_name'   => $stored['file_name'],
            ]);
        }
    }

    /**
     * Create/update/delete guarantors submitted with the Customer form.
     */
    protected function syncGuarantors(Request $request, Customer $customer): void
    {
        foreach ($request->input('removed_guarantor_ids', []) as $removedId) {
            $guarantor = CustomerGuarantor::where('customer_id', $customer->id)->find($removedId);

            if ($guarantor) {
                if ($guarantor->photo) {
                    File::delete(public_path($guarantor->photo));
                }
                $guarantor->delete();
            }
        }

        foreach ($request->input('guarantors', []) as $index => $row) {
            if (empty($row['name']) && empty($row['mobile'])) {
                continue;
            }

            $data = [
                'name'     => $row['name'] ?? null,
                'relation' => $row['relation'] ?? null,
                'mobile'   => $row['mobile'] ?? null,
                'nid'      => $row['nid'] ?? null,
                'address'  => $row['address'] ?? null,
            ];

            $photoFile = $request->file("guarantors.$index.photo");

            if (! empty($row['id'])) {
                $guarantor = CustomerGuarantor::where('customer_id', $customer->id)->find($row['id']);

                if (! $guarantor) {
                    continue;
                }

                if ($photoFile instanceof UploadedFile) {
                    if ($guarantor->photo) {
                        File::delete(public_path($guarantor->photo));
                    }
                    $data['photo'] = Helper::upload('guarantor_' . time() . '_' . uniqid(), $photoFile, 'uploads/customers/guarantors')['image_path'];
                }

                $guarantor->update($data);
            } else {
                if ($photoFile instanceof UploadedFile) {
                    $data['photo'] = Helper::upload('guarantor_' . time() . '_' . uniqid(), $photoFile, 'uploads/customers/guarantors')['image_path'];
                }

                $data['customer_id'] = $customer->id;

                CustomerGuarantor::create($data);
            }
        }
    }

    /**
     * Move a raw (non-image) uploaded file into public storage, preserving its extension.
     */
    protected function storeRawFile(UploadedFile $file, string $directory): array
    {
        if (! File::isDirectory(public_path($directory))) {
            File::makeDirectory(public_path($directory), 0755, true, true);
        }

        $extension = $file->getClientOriginalExtension();
        $fileName  = 'doc_' . time() . '_' . uniqid() . ($extension ? '.' . $extension : '');

        $file->move(public_path($directory), $fileName);

        return [
            'file_path' => $directory . '/' . $fileName,
            'file_name' => $file->getClientOriginalName(),
        ];
    }

    protected function generateCustomerCode(): string
    {
        $next = (Customer::max('id') ?? 0) + 1;

        return 'CUS-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * @return array{areas: \Illuminate\Support\Collection}
     */
    protected function formData(): array
    {
        return [
            'areas' => Area::where('status', true)->orderBy('name')->get(),
        ];
    }

    protected function validateCustomer(Request $request, ?Customer $customer = null): array
    {
        $validated = $request->validate([
            'area_id'               => ['required', 'exists:areas,id'],
            'name'                  => ['required', 'string', 'max:255'],
            'description'           => ['nullable', 'string'],
            'mobile'                => ['required', 'string', 'max:20'],
            'alternative_mobile'    => ['nullable', 'string', 'max:20'],
            'nid'                   => ['nullable', 'string', 'max:50'],
            'gender'                => ['nullable', Rule::in(['male', 'female', 'other'])],
            'father_name'           => ['nullable', 'string', 'max:255'],
            'occupation'            => ['nullable', 'string', 'max:255'],
            'address'               => ['required', 'string'],
            'photo'                 => ['nullable', 'image', 'max:4096'],
            'nid_image'             => ['nullable', 'image', 'max:4096'],
            'other_documents'       => ['nullable', 'array'],
            'other_documents.*'     => ['file', 'max:10240'],
            'guarantors'            => ['nullable', 'array'],
            'guarantors.*.id'       => ['nullable', 'integer', 'exists:customer_guarantors,id'],
            'guarantors.*.name'     => ['required_with:guarantors.*.mobile', 'nullable', 'string', 'max:255'],
            'guarantors.*.relation' => ['nullable', 'string', 'max:255'],
            'guarantors.*.mobile'   => ['required_with:guarantors.*.name', 'nullable', 'string', 'max:20'],
            'guarantors.*.nid'      => ['nullable', 'string', 'max:50'],
            'guarantors.*.address'  => ['nullable', 'string'],
            'guarantors.*.photo'    => ['nullable', 'image', 'max:4096'],
        ]);

        unset($validated['photo'], $validated['nid_image'], $validated['other_documents'], $validated['guarantors']);

        return $validated;
    }
}
