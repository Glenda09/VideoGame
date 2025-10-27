<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class Inventory extends Model
{
    /** @use HasFactory<\Database\Factories\InventoryFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function decrease(int $amount): void
    {
        if ($amount < 0) {
            throw new RuntimeException('Invalid quantity.');
        }

        if ($amount === 0) {
            return;
        }

        if ($this->quantity < $amount) {
            throw new RuntimeException('Insufficient inventory.');
        }

        $this->decrement('quantity', $amount);
    }

    public function increase(int $amount): void
    {
        if ($amount < 0) {
            throw new RuntimeException('Invalid quantity.');
        }

        if ($amount === 0) {
            return;
        }

        $this->increment('quantity', $amount);
    }
}
