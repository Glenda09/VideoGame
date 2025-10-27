<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalKey extends Model
{
    /** @use HasFactory<\Database\Factories\DigitalKeyFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'order_item_id',
        'code',
        'redeemed_at',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereNull('order_item_id');
    }

    public function assignTo(OrderItem $item): void
    {
        $this->update([
            'order_item_id' => $item->id,
        ]);
    }

    public function markRedeemed(): void
    {
        $this->update(['redeemed_at' => now()]);
    }
}
