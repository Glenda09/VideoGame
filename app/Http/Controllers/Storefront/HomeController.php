<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\CacheKeys;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featured = Cache::remember(CacheKeys::featuredProducts(), 3600, function () {
            return Product::query()
                ->with(['images', 'platforms', 'category'])
                ->withAvg('approvedReviews as rating_avg', 'rating')
                ->withCount('approvedReviews')
                ->active()
                ->latest('release_date')
                ->take(8)
                ->get();
        });

        $topRated = Product::query()
            ->with(['images', 'platforms'])
            ->withAvg('approvedReviews as rating_avg', 'rating')
            ->withCount('approvedReviews')
            ->active()
            ->orderByDesc('rating_avg')
            ->take(6)
            ->get();

        $digitalHighlights = Product::query()
            ->with(['images', 'platforms'])
            ->withAvg('approvedReviews as rating_avg', 'rating')
            ->withCount('approvedReviews')
            ->digital()
            ->active()
            ->latest()
            ->take(6)
            ->get();

        $categories = Cache::remember(CacheKeys::categoryTree(), 3600, function () {
            return Category::query()
                ->with('children')
                ->root()
                ->orderBy('name')
                ->get();
        });

        return view('storefront.home', [
            'featured' => $featured,
            'topRated' => $topRated,
            'digitalHighlights' => $digitalHighlights,
            'categories' => $categories,
        ]);
    }
}
