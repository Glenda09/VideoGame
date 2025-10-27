<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'sku',
        'title',
        'slug',
        'description',
        'price_cents',
        'currency',
        'is_digital',
        'active',
        'cover_image',
        'release_date',
        'category_id',
    ];

    protected $casts = [
        'is_digital' => 'bool',
        'active' => 'bool',
        'release_date' => 'date',
    ];

    protected string $slugSource = 'title';

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class)
            ->withTimestamps();
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('approved', true);
    }

    public function digitalKeys(): HasMany
    {
        return $this->hasMany(DigitalKey::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeDigital(Builder $query): Builder
    {
        return $query->where('is_digital', true);
    }

    public function scopePhysical(Builder $query): Builder
    {
        return $query->where('is_digital', false);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = "%{$term}%";

        return $query->where(fn (Builder $builder) => $builder
            ->where('title', 'like', $term)
            ->orWhere('description', 'like', $term)
            ->orWhere('sku', 'like', $term));
    }

    public function scopeForCategory(Builder $query, ?Category $category): Builder
    {
        if (!$category) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($category): void {
            $builder->where('category_id', $category->id)
                ->orWhereHas('category', fn (Builder $q) => $q->where('parent_id', $category->id));
        });
    }

    public function scopeForPlatform(Builder $query, ?Platform $platform): Builder
    {
        if (!$platform) {
            return $query;
        }

        return $query->whereHas('platforms', fn (Builder $builder) => $builder->where('platforms.id', $platform->id));
    }

    public function scopePriceBetween(Builder $query, ?int $min, ?int $max): Builder
    {
        if ($min !== null) {
            $query->where('price_cents', '>=', $min);
        }

        if ($max !== null) {
            $query->where('price_cents', '<=', $max);
        }

        return $query;
    }

    public function price(): Money
    {
        return new Money($this->price_cents, $this->currency);
    }

    public function hasStock(int $quantity = 1): bool
    {
        if ($this->is_digital) {
            return true;
        }

        $available = $this->inventory?->quantity ?? 0;

        return $available >= $quantity;
    }

    public function coverImageUrl(): string
    {
        if (!$this->cover_image) {
            return asset('images/placeholders/game-cover.svg');
        }
        if (Str::startsWith($this->cover_image, ['http://', 'https://'])) {
            return $this->cover_image;
        }

        if (Str::startsWith($this->cover_image, 'data:')) {
            return $this->cover_image;
        }

        if (Str::startsWith($this->cover_image, 'images/')) {
            return asset($this->cover_image);
        }

        return Storage::url($this->cover_image);
    }

    public function availableDigitalKey(): ?DigitalKey
    {
        if (!$this->is_digital) {
            return null;
        }

        return $this->digitalKeys()
            ->whereNull('order_item_id')
            ->orderBy('created_at')
            ->first();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
