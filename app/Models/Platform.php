<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Platform extends Model
{
    /** @use HasFactory<\Database\Factories\PlatformFactory> */
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected string $slugSource = 'name';

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withTimestamps();
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = "%{$term}%";

        return $query->where(fn (Builder $builder) => $builder
            ->where('name', 'like', $term)
            ->orWhere('slug', 'like', $term));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
