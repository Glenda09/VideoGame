<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'user_id',
        'subtotal_cents',
        'discount_cents',
        'tax_cents',
        'total_cents',
        'currency',
        'status',
        'coupon_code',
        'billing_data',
        'shipping_data',
    ];

    protected $casts = [
        'subtotal_cents' => 'integer',
        'discount_cents' => 'integer',
        'tax_cents' => 'integer',
        'total_cents' => 'integer',
        'status' => OrderStatus::class,
        'billing_data' => 'array',
        'shipping_data' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $order): void {
            if (blank($order->number)) {
                $order->number = self::generateNumber();
            }
        });
    }

    public static function generateNumber(): string
    {
        return 'ORD-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    public function digitalKeys(): HasManyThrough
    {
        return $this->hasManyThrough(
            DigitalKey::class,
            OrderItem::class,
            'order_id',
            'order_item_id'
        );
    }

    public function scopeStatus(Builder $query, OrderStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::Paid);
    }

    public function subtotal(): Money
    {
        return new Money($this->subtotal_cents, $this->currency);
    }

    public function tax(): Money
    {
        return new Money($this->tax_cents, $this->currency);
    }

    public function discount(): Money
    {
        return new Money($this->discount_cents, $this->currency);
    }

    public function total(): Money
    {
        return new Money($this->total_cents, $this->currency);
    }

    public function isPaid(): bool
    {
        return $this->status === OrderStatus::Paid;
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    public function requiresPayment(): bool
    {
        return in_array($this->status, [OrderStatus::Pending, OrderStatus::RequiresPayment], true);
    }

    public function canRetryPayment(): bool
    {
        return $this->status->canRetryPayment();
    }

    public function markAsPaid(): void
    {
        $this->update(['status' => OrderStatus::Paid]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => OrderStatus::Failed]);
    }

    public function markAsCanceled(): void
    {
        $this->update(['status' => OrderStatus::Canceled]);
    }

    public function hasDigitalItems(): bool
    {
        return $this->items->contains(fn (OrderItem $item) => $item->is_digital);
    }

    public function requiresShipping(): bool
    {
        return $this->items->contains(fn (OrderItem $item) => !$item->is_digital);
    }
}
