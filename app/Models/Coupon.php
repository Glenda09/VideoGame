<?php

namespace App\Models;

use App\Enums\CouponType;
use App\Support\Money;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    /** @use HasFactory<\Database\Factories\CouponFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'starts_at',
        'ends_at',
        'usage_limit',
        'used_count',
        'active',
    ];

    protected $casts = [
        'type' => CouponType::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'active' => 'bool',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'coupon_code', 'code');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeCode(Builder $query, string $code): Builder
    {
        return $query->whereRaw('LOWER(code) = ?', [mb_strtolower($code)]);
    }

    public function scopeValid(Builder $query, ?CarbonInterface $at = null): Builder
    {
        $at = $at ?? now();

        return $query->active()
            ->where(function (Builder $builder) use ($at): void {
                $builder
                    ->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $at);
            })
            ->where(function (Builder $builder) use ($at): void {
                $builder
                    ->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $at);
            })
            ->where(function (Builder $builder): void {
                $builder
                    ->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }

    public function canBeApplied(?CarbonInterface $at = null): bool
    {
        $at = $at ?? now();

        $validPeriod = ($this->starts_at === null || $this->starts_at->lte($at))
            && ($this->ends_at === null || $this->ends_at->gte($at));

        $usageAvailable = $this->usage_limit === null || $this->used_count < $this->usage_limit;

        return $this->active && $validPeriod && $usageAvailable;
    }

    public function applyTo(Money $subtotal): Money
    {
        $discount = match ($this->type) {
            CouponType::Fixed => min($this->value * 100, $subtotal->amount),
            CouponType::Percent => min((int) round($subtotal->amount * ($this->value / 100)), $subtotal->amount),
        };

        return new Money($discount, $subtotal->currency);
    }

    public function registerUsage(): void
    {
        $this->increment('used_count');
    }

    public function resetUsage(): void
    {
        $this->update(['used_count' => 0]);
    }
}
