<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Platform;
use App\Models\Product;
use App\Support\CacheKeys;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        return $this->renderCatalog($request);
    }

    public function category(Request $request, Category $category): View
    {
        return $this->renderCatalog($request, $category);
    }

    public function platform(Request $request, Platform $platform): View
    {
        return $this->renderCatalog($request, null, $platform);
    }

    private function renderCatalog(Request $request, ?Category $category = null, ?Platform $platform = null): View
    {
        $query = Product::query()
            ->with(['images', 'platforms', 'category'])
            ->withAvg('approvedReviews as rating_avg', 'rating')
            ->withCount('approvedReviews')
            ->active();

        $search = trim((string) $request->input('search'));
        if ($search !== '') {
            $query->search($search);
        }

        $selectedCategory = $category;
        if (!$selectedCategory && $request->filled('category')) {
            $selectedCategory = Category::query()->where('slug', $request->string('category'))->first();
        }

        if ($selectedCategory) {
            $query->forCategory($selectedCategory);
        }

        $selectedPlatform = $platform;
        if (!$selectedPlatform && $request->filled('platform')) {
            $selectedPlatform = Platform::query()->where('slug', $request->string('platform'))->first();
        }

        if ($selectedPlatform) {
            $query->forPlatform($selectedPlatform);
        }

        $type = $request->string('type')->toString();
        if ($type === 'digital') {
            $query->digital();
        } elseif ($type === 'physical') {
            $query->physical();
        }

        $minPrice = $this->toCents($request->input('price_min'));
        $maxPrice = $this->toCents($request->input('price_max'));
        $query->priceBetween($minPrice, $maxPrice);

        $sort = $request->string('sort')->toString() ?: 'latest';
        $this->applySorting($query, $sort);

        /** @var LengthAwarePaginator $products */
        $products = $query
            ->paginate(12)
            ->withQueryString();

        $categories = Cache::remember(CacheKeys::categoryTree(), 3600, function () {
            return Category::query()
                ->with('children')
                ->root()
                ->orderBy('name')
                ->get();
        });

        $platforms = Cache::remember(CacheKeys::platforms(), 3600, function () {
            return Platform::query()
                ->orderBy('name')
                ->get();
        });

        $filters = [
            'search' => $search,
            'type' => $type,
            'price_min' => $request->input('price_min'),
            'price_max' => $request->input('price_max'),
            'sort' => $sort,
            'category' => $selectedCategory?->slug,
            'platform' => $selectedPlatform?->slug,
        ];

        $pageTitle = 'Catálogo';
        if ($selectedCategory) {
            $pageTitle = $selectedCategory->name.' Games';
        } elseif ($selectedPlatform) {
            $pageTitle = $selectedPlatform->name.' Games';
        } elseif ($search !== '') {
            $pageTitle = "Resultados para '{$search}'";
        }

        return view('storefront.catalog', [
            'products' => $products,
            'categories' => $categories,
            'platforms' => $platforms,
            'filters' => $filters,
            'selectedCategory' => $selectedCategory,
            'selectedPlatform' => $selectedPlatform,
            'pageTitle' => $pageTitle,
        ]);
    }

    private function toCents(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $numeric = (float) $value;

        if ($numeric <= 0) {
            return null;
        }

        return (int) round($numeric * 100);
    }

    private function applySorting($query, string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('price_cents'),
            'price_desc' => $query->orderByDesc('price_cents'),
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('release_date'),
            'oldest' => $query->orderBy('release_date'),
            default => $query->orderByDesc('release_date'),
        };
    }
}
