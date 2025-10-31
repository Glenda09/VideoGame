<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    /** @use HasFactory<\Database\Factories\CartItemFactory> */
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'unit_price_cents',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Relación con el carrito
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Relación con el producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Devuelve el precio unitario como objeto Money
     */
    public function unitPrice(): Money
    {
        // Intenta obtener la moneda desde el carrito o el producto, o usa USD como fallback
        $currency = $this->cart?->currency ?? $this->product?->currency ?? 'USD';

        // Asegurarse de que unit_price_cents siempre sea entero
        $amount = (int) ($this->unit_price_cents ?? 0);

        return new Money($amount, $currency);
    }

    /**
     * Devuelve el total de la línea (precio unitario * cantidad)
     * Garantiza que nunca sea null, incluso si faltan datos
     */
    public function lineTotal(): Money
    {
        // Precio unitario (en centavos)
        $unit = (int) ($this->unit_price_cents ?? 0);
        // Cantidad (mínimo 0)
        $qty = (int) ($this->quantity ?? 0);

        // Total
        $amount = $unit * $qty;

        // Determinar la moneda
        $currency = $this->cart?->currency ?? $this->product?->currency ?? 'USD';

        // Siempre devuelve un objeto Money (nunca null)
        return new Money($amount, $currency);
    }
}
