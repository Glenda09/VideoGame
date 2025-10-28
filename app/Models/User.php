<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function activeCart(): HasOne
    {
        return $this->hasOne(Cart::class)->active()->latestOfMany();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlist(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlists');
    }

    public function isAdmin(): bool
    {
        return $this->isSuperAdmin();
    }

    public function isSuperAdmin(): bool
    {
        if ($this->role instanceof Role) {
            return $this->role->isSuperAdmin();
        }

        if (is_string($this->role)) {
            return Role::tryFrom($this->role)?->isSuperAdmin() ?? false;
        }

        $attributes = $this->getAttributes();

        if (array_key_exists('is_admin', $attributes)) {
            return (bool) $attributes['is_admin'];
        }

        return false;
    }
}
