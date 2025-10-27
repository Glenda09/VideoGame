<?php

namespace App\Support;

use InvalidArgumentException;

class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency = 'USD'
    ) {
        if ($this->amount < 0) {
            throw new InvalidArgumentException('Amount must be zero or positive.');
        }
    }

    public static function from(int|float|string|self $value, string $currency = 'USD'): self
    {
        if ($value instanceof self) {
            return $value;
        }

        if (is_int($value)) {
            return new self($value, $currency);
        }

        if (is_float($value) || is_string($value)) {
            $normalized = (int) round(((float) $value) * 100);

            if ($normalized < 0) {
                throw new InvalidArgumentException('Amount must be zero or positive.');
            }

            return new self($normalized, $currency);
        }

        throw new InvalidArgumentException('Unsupported money value.');
    }

    public function add(self $other): self
    {
        $this->guardCurrency($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->guardCurrency($other);

        if ($other->amount > $this->amount) {
            throw new InvalidArgumentException('Resulting amount would be negative.');
        }

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiply(float $multiplier): self
    {
        if ($multiplier < 0) {
            throw new InvalidArgumentException('Multiplier must be positive.');
        }

        $value = (int) round($this->amount * $multiplier);

        return new self($value, $this->currency);
    }

    public function percentage(float $percentage): self
    {
        if ($percentage < 0) {
            throw new InvalidArgumentException('Percentage must be positive.');
        }

        $value = (int) round(($this->amount * $percentage) / 100);

        return new self($value, $this->currency);
    }

    public function toDecimal(): float
    {
        return $this->amount / 100;
    }

    public function format(int $precision = 2, string $locale = 'en_US'): string
    {
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($this->toDecimal(), $this->currency);
    }

    public function __toString(): string
    {
        return $this->format();
    }

    private function guardCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Currency mismatch.');
        }
    }
}
