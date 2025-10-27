<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        $product->load([
            'images',
            'platforms',
            'category',
            'approvedReviews' => fn ($query) => $query->with('user')->latest(),
        ])->loadCount('approvedReviews');

        $averageRating = round((float) $product->approvedReviews->avg('rating'), 1);
        $reviewCount = $product->approved_reviews_count;

        $relatedProducts = Product::query()
            ->with(['images', 'platforms'])
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->active()
            ->take(4)
            ->get();

        return view('storefront.product', [
            'product' => $product,
            'averageRating' => $averageRating,
            'reviewCount' => $reviewCount,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
