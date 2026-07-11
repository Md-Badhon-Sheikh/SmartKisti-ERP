<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('products.index');
    }

    public function Datatable(Request $request): JsonResponse
    {
        $products = Product::with(['category', 'subCategory', 'brand', 'manufacturer'])->select('products.*');

        return DataTables::eloquent($products)
            ->addColumn('category', fn (Product $product) => $product->category->name)
            ->addColumn('sub_category', fn (Product $product) => $product->subCategory->name)
            ->addColumn('brand_manufacturer', fn (Product $product) => $product->brand?->name ?? $product->manufacturer?->name ?? '—')
            ->addColumn('type', fn (Product $product) => '<span class="badge badge-light-'.($product->product_type === 'ready' ? 'success' : 'warning').'">'
                .($product->product_type === 'ready' ? __('Ready') : __('Custom')).'</span>')
            ->editColumn('selling_price', fn (Product $product) => number_format($product->selling_price, 2))
            ->editColumn('stock', fn (Product $product) => $product->product_type === 'ready' ? $product->stock : '—')
            ->addColumn('status', fn (Product $product) => '<span class="badge badge-light-'.($product->status ? 'success' : 'danger').'">'
                .($product->status ? __('Active') : __('Inactive')).'</span>')
            ->addColumn('action', function (Product $product) use ($request) {
                $html = '<a href="'.route('products.show', $product).'" class="btn btn-sm btn-icon btn-light-success me-2" title="'.__('View').'"><i class="fas fa-eye"></i></a>';

                if ($request->user()->hasAnyRole(['super-admin', 'admin', 'manager'])) {
                    $html .= '<a href="'.route('products.edit', $product).'" class="btn btn-sm btn-icon btn-light-primary me-2" title="'.__('Edit').'"><i class="fas fa-pen"></i></a>';
                    $html .= '<form action="'.route('products.destroy', $product).'" method="POST" class="d-inline delete-form">'
                        .csrf_field().method_field('DELETE')
                        .'<button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="'.__('Delete').'"><i class="fas fa-trash"></i></button>'
                        .'</form>';
                }

                return $html;
            })
            ->rawColumns(['type', 'status', 'action'])
            ->toJson();
    }

    public function create(): View
    {
        return view('products.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);
        $validated['status'] = true;

        Product::create($validated);

        return redirect()->route('products.index')->with('status', __('Product created successfully.'));
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'subCategory', 'brand', 'manufacturer']);

        return view('products.show', ['product' => $product]);
    }

    public function edit(Product $product): View
    {
        return view('products.edit', array_merge(['product' => $product], $this->formData()));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product);
        $validated['status'] = $request->boolean('status');

        $product->update($validated);

        return redirect()->route('products.index')->with('status', __('Product updated successfully.'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('status', __('Product deleted.'));
    }

    /**
     * @return array{categories: \Illuminate\Support\Collection, subCategories: \Illuminate\Support\Collection, brands: \Illuminate\Support\Collection, manufacturers: \Illuminate\Support\Collection}
     */
    protected function formData(): array
    {
        return [
            'categories' => Category::where('status', true)->orderBy('name')->get(),
            'subCategories' => SubCategory::where('status', true)->orderBy('name')->get(),
            'brands' => Brand::where('status', true)->orderBy('name')->get(),
            'manufacturers' => Manufacturer::where('status', true)->orderBy('name')->get(),
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
            'manufacturer_id' => ['nullable', 'exists:manufacturers,id'],
            'model' => ['nullable', 'string', 'max:255'],
            'imei_serial' => ['nullable', 'string', 'max:255'],
            'wood_type' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'polish' => ['nullable', 'string', 'max:255'],
            'warranty' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($product?->id)],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
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

        return $validated;
    }
}
