<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'provider',
        'provider_reference',
        'status',
        'amount_cents',
        'raw_payload',
        'received_at',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'raw_payload' => 'array',
        'received_at' => 'datetime',
        'amount_cents' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function amount(): Money
    {
        $currency = $this->order?->currency ?? 'USD';

        return new Money($this->amount_cents, $currency);
    }

    public function markApproved(array $payload = []): void
    {
        $this->update([
            'status' => PaymentStatus::Approved,
            'raw_payload' => $payload,
            'received_at' => now(),
        ]);
    }

    public function markDeclined(array $payload = []): void
    {
        $this->update([
            'status' => PaymentStatus::Declined,
            'raw_payload' => $payload,
            'received_at' => now(),
        ]);
    }

    public function markVoided(array $payload = []): void
    {
        $this->update([
            'status' => PaymentStatus::Voided,
            'raw_payload' => $payload,
            'received_at' => now(),
        ]);
    }
}
