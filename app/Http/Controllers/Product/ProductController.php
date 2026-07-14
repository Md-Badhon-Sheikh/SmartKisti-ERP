<?php

namespace App\Http\Controllers\Product;

use App\Enums\GlobalConstant;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display the index page.
     */
    public function Index()
    {
        return view('products.index');
    }

    /**
     * Return DataTable JSON for the listing.
     */
    public function Datatable(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'subCategory', 'brand'])->select('products.*');

        return DataTables::eloquent($query)
            ->addColumn('category', fn (Product $product) => $product->category->name)
            ->addColumn('sub_category', fn (Product $product) => $product->subCategory->name)
            ->addColumn('brand_manufacturer', fn (Product $product) => $product->brand?->name ?? $product->manufacturerName() ?? '—')
            ->addColumn('type', fn (Product $product) => '<span class="badge badge-light-'.($product->product_type === 'ready' ? 'success' : 'warning').'">'
                .($product->product_type === 'ready' ? __('Ready') : __('Custom')).'</span>')
            ->editColumn('selling_price', fn (Product $product) => number_format($product->selling_price, 2))
            ->editColumn('stock', fn (Product $product) => $product->product_type === 'ready' ? $product->stock : '—')
            ->addColumn('status', function (Product $product) {
                $checked    = $product->status ? 'checked' : '';
                $badgeClass = $product->status ? 'badge-light-success' : 'badge-light-danger';
                $label      = $product->status ? __('Active') : __('Inactive');
                return '
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge ' . $badgeClass . '">' . $label . '</span>
                        <div class="form-check form-switch ms-2 mb-0">
                            <input class="form-check-input status-toggle" type="checkbox"
                                data-id="' . $product->id . '" ' . $checked . '>
                        </div>
                    </div>';
            })
            ->addColumn('action', function (Product $product) use ($request) {
                $html = '<div class="d-flex justify-content-end gap-2">'
                    . '<button class="btn btn-light-info btn-view px-4 py-2" data-id="' . $product->id . '">'
                    . '<i class="fas fa-eye"></i>'
                    . '</button>';

                if ($request->user()->hasAnyRole(['super-admin', 'admin', 'manager'])) {
                    $html .= '<a href="' . route('products.edit', $product->id) . '" class="btn btn-sm btn-light-primary px-4 py-2">'
                        . '<i class="fas fa-edit"></i>'
                        . '</a>';

                    $html .= '<button class="btn btn-sm btn-light-danger btn-delete px-4 py-2" data-id="' . $product->id . '">'
                        . '<i class="fas fa-trash"></i>'
                        . '</button>';
                }

                $html .= '</div>';

                return $html;
            })
            ->rawColumns(['type', 'status', 'action'])
            ->make(true);
    }

    /**
     * Display the create page.
     */
    public function Create(): View
    {
        return view('products.create', $this->formData());
    }

    /**
     * Store a newly created Product.
     */
    public function Store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);
        $validated['status'] = true;

        $product = Product::create($validated);

        $this->storeImages($request, $product);

        return redirect()->route('products.index')->with('status', __('Product created successfully.'));
    }

    /**
     * Display the edit page.
     */
    public function Edit(Product $product): View
    {
        $product->load('images');

        return view('products.edit', array_merge(['product' => $product], $this->formData()));
    }

    /**
     * Show a single Product (used by the view modal's AJAX fetch).
     */
    public function Show(int $id): JsonResponse
    {
        $product = Product::with(['category', 'subCategory', 'brand', 'images'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                => $product->id,
                'category_id'       => $product->category_id,
                'category_name'     => $product->category->name,
                'sub_category_id'   => $product->sub_category_id,
                'sub_category_name' => $product->subCategory->name,
                'name'              => $product->name,
                'product_type'      => $product->product_type,
                'brand_id'          => $product->brand_id,
                'brand_name'        => $product->brand?->name,
                'manufacturer_code' => $product->manufacturer_code,
                'manufacturer_name' => $product->manufacturerName(),
                'model'             => $product->model,
                'imei_serial'       => $product->imei_serial,
                'wood_type'         => $product->wood_type,
                'wood_type_name'    => $product->woodTypeName(),
                'color'             => $product->color,
                'color_name'        => $product->colorName(),
                'size'              => $product->size,
                'polish'            => $product->polish,
                'warranty'          => $product->warranty,
                'sku'               => $product->sku,
                'purchase_price'    => $product->purchase_price,
                'selling_price'     => $product->selling_price,
                'stock'             => $product->stock,
                'status'            => $product->status,
                'images'            => $product->images->map(fn (ProductImage $image) => [
                    'id'  => $image->id,
                    'url' => asset($image->image_path),
                ]),
                'created_at' => $product->created_at?->format('d M Y, h:i A'),
            ],
        ]);
    }

    /**
     * Update an existing Product.
     */
    public function Update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product);
        $validated['status'] = $request->boolean('status');

        $product->update($validated);

        $this->storeImages($request, $product);

        return redirect()->route('products.index')->with('status', __('Product updated successfully.'));
    }

    /**
     * Delete a Product. (POST only)
     */
    public function Delete(int $id): JsonResponse
    {
        $product = Product::with('images')->findOrFail($id);

        foreach ($product->images as $image) {
            File::delete(public_path($image->image_path));
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }

    /**
     * Toggle the status.
     */
    public function ToggleStatus(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => !$product->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'status'  => $product->status,
        ]);
    }

    /**
     * Delete a single image belonging to a Product. (POST only)
     */
    public function DeleteImage(int $id, int $imageId): JsonResponse
    {
        $image = ProductImage::where('product_id', $id)->findOrFail($imageId);

        File::delete(public_path($image->image_path));
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.',
        ]);
    }

    /**
     * Upload and attach any submitted images to the given Product.
     */
    protected function storeImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $sortOrder = $product->images()->max('sort_order') ?? 0;

        foreach ($request->file('images') as $file) {
            $sortOrder++;
            $name = 'product_' . $product->id . '_' . time() . '_' . $sortOrder . '_' . uniqid();
            $uploaded = Helper::upload($name, $file, 'uploads/products');

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $uploaded['image_path'],
                'image_name' => $uploaded['image_name'],
                'sort_order' => $sortOrder,
            ]);
        }
    }

    /**
     * @return array{categories: \Illuminate\Support\Collection, subCategories: \Illuminate\Support\Collection, brands: \Illuminate\Support\Collection, manufacturers: \Illuminate\Support\Collection, woodTypes: \Illuminate\Support\Collection, colors: \Illuminate\Support\Collection}
     */
    protected function formData(): array
    {
        return [
            'categories'    => Category::where('status', true)->orderBy('name')->get(),
            'subCategories' => SubCategory::where('status', true)->orderBy('name')->get(),
            'brands'        => Brand::where('status', true)->orderBy('name')->get(),
            'manufacturers' => GlobalConstant::activeManufacturers(),
            'woodTypes'     => GlobalConstant::activeWoodTypes(),
            'colors'        => GlobalConstant::activeColors(),
        ];
    }

    protected function validateProduct(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sub_category_id' => ['required', 'exists:sub_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'product_type' => ['required', Rule::in(['ready', 'custom'])],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'manufacturer_code' => ['nullable', Rule::in(collect(GlobalConstant::MANUFACTURERS)->pluck('code')->all())],
            'model' => ['nullable', 'string', 'max:255'],
            'imei_serial' => ['nullable', 'string', 'max:255'],
            'wood_type' => ['nullable', Rule::in(collect(GlobalConstant::WOOD_TYPE)->pluck('code')->all())],
            'color' => ['nullable', Rule::in(collect(GlobalConstant::COLOR)->pluck('code')->all())],
            'size' => ['nullable', 'string', 'max:255'],
            'polish' => ['nullable', 'string', 'max:255'],
            'warranty' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($product?->id)],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
        ]);

        $category = Category::findOrFail($validated['category_id']);
        $subCategory = SubCategory::findOrFail($validated['sub_category_id']);

        if ($subCategory->category_id !== $category->id) {
            throw ValidationException::withMessages([
                'sub_category_id' => __('The selected sub category does not belong to the selected category.'),
            ]);
        }

        if ($category->brand_required && empty($validated['brand_id'])) {
            throw ValidationException::withMessages([
                'brand_id' => __('Brand is required for this category.'),
            ]);
        }

        $validated['stock'] = $validated['product_type'] === 'ready' ? ($validated['stock'] ?? 0) : 0;

        unset($validated['images']);

        return $validated;
    }
}
