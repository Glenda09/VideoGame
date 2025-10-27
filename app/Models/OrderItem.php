<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'title_snapshot',
        'unit_price_cents',
        'quantity',
        'is_digital',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'is_digital' => 'bool',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function digitalKey(): HasOne
    {
        return $this->hasOne(DigitalKey::class);
    }

    public function unitPrice(): Money
    {
        $currency = $this->order?->currency ?? $this->product?->currency ?? 'USD';

        return new Money($this->unit_price_cents, $currency);
    }

    public function lineTotal(): Money
    {
        return $this->unitPrice()->multiply($this->quantity);
    }
}
