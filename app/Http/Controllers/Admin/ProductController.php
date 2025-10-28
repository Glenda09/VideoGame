<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Platform;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with(['category', 'platforms'])
            ->latest()
            ->paginate(15);

        return view('admin.products.index', [
            'products' => $products,
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product([
                'currency' => 'USD',
                'active' => true,
                'is_digital' => true,
            ]),
            'categories' => Category::query()->orderBy('name')->get(),
            'platforms' => Platform::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateProduct($request);

        DB::transaction(function () use ($data): void {
            /** @var Product $product */
            $product = Product::query()->create($data['attributes']);

            $product->platforms()->sync($data['platforms']);

            $this->syncInventory($product, $data['inventory_quantity']);
        });

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Producto creado correctamente.');
    }

    public function edit(Product $product): View
    {
        $product->load(['platforms', 'inventory']);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::query()->orderBy('name')->get(),
            'platforms' => Platform::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validateProduct($request, $product);

        DB::transaction(function () use ($data, $product): void {
            $product->update($data['attributes']);

            $product->platforms()->sync($data['platforms']);

            $this->syncInventory($product, $data['inventory_quantity']);
        });

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        DB::transaction(function () use ($product): void {
            $product->platforms()->detach();
            $product->inventory()?->delete();
            $product->delete();
        });

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Producto eliminado.');
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        $product->update([
            'active' => !$product->active,
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Estado del producto actualizado.');
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($product?->id),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'is_digital' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
            'cover_image' => ['nullable', 'string', 'max:2048'],
            'release_date' => ['nullable', 'date'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'platforms' => ['nullable', 'array'],
            'platforms.*' => ['exists:platforms,id'],
            'inventory_quantity' => ['nullable', 'integer', 'min:0'],
        ]);

        $priceCents = (int) round($validated['price'] * 100);

        $attributes = [
            'title' => $validated['title'],
            'sku' => $validated['sku'],
            'description' => $validated['description'] ?? null,
            'price_cents' => $priceCents,
            'currency' => strtoupper($validated['currency']),
            'is_digital' => (bool) ($validated['is_digital'] ?? false),
            'active' => (bool) ($validated['active'] ?? false),
            'cover_image' => $validated['cover_image'] ?? null,
            'release_date' => $validated['release_date'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
        ];

        return [
            'attributes' => $attributes,
            'platforms' => $validated['platforms'] ?? [],
            'inventory_quantity' => $validated['inventory_quantity'] ?? null,
        ];
    }

    private function syncInventory(Product $product, ?int $quantity): void
    {
        if ($product->is_digital) {
            if ($product->inventory) {
                $product->inventory()->delete();
            }

            return;
        }

        $quantity ??= 0;

        $product->inventory()->updateOrCreate(
            [],
            ['quantity' => $quantity],
        );
    }
}

