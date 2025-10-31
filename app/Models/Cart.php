<?php

namespace App\Models;

use App\Support\CartTotals;
use App\Support\Money;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class Cart extends Model
{
    /** @use HasFactory<\Database\Factories\CartFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_token',
        'currency',
        'vat_rate',
        'coupon_code',
        'expires_at',
    ];

    protected $casts = [
        'vat_rate' => 'float',
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $cart): void {
            $cart->currency ??= config('store.currency', 'USD');
            $cart->vat_rate ??= config('store.vat_rate', 0.0);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $builder): void {
            $builder->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeForGuest(Builder $query, string $token): Builder
    {
        return $query->where('guest_token', $token);
    }

       /**
     * Accessor para $cart->subtotal (devuelve un objeto Money).
     * Laravel usará este método cuando accedas a $cart->subtotal en vistas.
     */
    public function getSubtotalAttribute(): Money
    {
        $amount = (int) $this->items->sum(
            fn (CartItem $item) => $item->lineTotal()->amount ?? 0
        );

        return new Money($amount, $this->currency);
    }

    /**
     * Método compatible para uso en código (si alguien llamaba a $cart->subtotal()).
     * Llamar a $cart->subtotalAmount() devuelve el mismo Money que el accessor.
     */
    public function subtotalAmount(): Money
    {
        return $this->getSubtotalAttribute();
    }


    public function totals(): CartTotals
    {
        return CartTotals::fromItems(
            $this->items,
            $this->coupon,
            $this->vat_rate,
            $this->currency
        );
    }

    public function addProduct(Product $product, int $quantity = 1): CartItem
    {
        if ($quantity < 1) {
            throw new RuntimeException('Quantity must be at least 1.');
        }

        $item = $this->items()
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->increment('quantity', $quantity);

            return $item->refresh();
        }

        return $this->items()->create([
            'product_id' => $product->id,
            'unit_price_cents' => $product->price_cents,
            'quantity' => $quantity,
        ]);
    }

    public function updateProductQuantity(Product $product, int $quantity): void
    {
        if ($quantity < 1) {
            $this->removeProduct($product);

            return;
        }

        $this->items()
            ->where('product_id', $product->id)
            ->update(['quantity' => $quantity]);
    }

    public function removeProduct(Product $product): void
    {
        $this->items()
            ->where('product_id', $product->id)
            ->delete();
    }

    public function clear(): void
    {
        $this->items()->delete();
        $this->update([
            'coupon_code' => null,
        ]);
    }

    public function applyCoupon(Coupon $coupon): void
    {
        if (!$coupon->canBeApplied()) {
            throw new RuntimeException('Coupon is not valid.');
        }

        $this->update(['coupon_code' => $coupon->code]);
    }

    public function removeCoupon(): void
    {
        $this->update(['coupon_code' => null]);
    }

    public function markExpiresAt(CarbonInterface $when): void
    {
        $this->update(['expires_at' => $when]);
    }

    public function ensureCurrency(string $currency): void
    {
        if ($this->currency !== $currency) {
            $this->update(['currency' => $currency]);

            $this->items()->update(['unit_price_cents' => DB::raw('unit_price_cents')]);
        }
    }

    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }
}
