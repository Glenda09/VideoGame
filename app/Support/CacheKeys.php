<?php

namespace App\Support;

class CacheKeys
{
    public static function categoryTree(): string
    {
        return 'catalog:category-tree';
    }

    public static function featuredProducts(): string
    {
        return 'catalog:featured-products';
    }

    public static function homepageBanners(): string
    {
        return 'catalog:home-banners';
    }

    public static function productDetail(int $productId): string
    {
        return "catalog:product:{$productId}";
    }

    public static function platforms(): string
    {
        return 'catalog:platforms';
    }
}
