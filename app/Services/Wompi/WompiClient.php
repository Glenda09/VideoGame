<?php

namespace App\Services\Wompi;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WompiClient
{
    public function createTransaction(Order $order): array
    {
        $payload = [
            'amount_in_cents' => $order->total_cents,
            'currency' => $order->currency,
            'customer_email' => data_get($order->billing_data, 'email', $order->user?->email),
            'payment_method_types' => ['CARD', 'PSE'],
            'redirect_url' => config('wompi.redirect_urls.success'),
            'reference' => $order->number,
            'customer_data' => [
                'full_name' => data_get($order->billing_data, 'name'),
                'phone_number' => data_get($order->billing_data, 'phone'),
                'legal_id' => data_get($order->billing_data, 'doc'),
                'legal_id_type' => 'CC',
            ],
        ];

        $response = Http::withToken(config('wompi.private_key'))
            ->acceptJson()
            ->post(rtrim(config('wompi.api_url'), '/').'/v1/transactions', $payload);

        if ($response->failed()) {
            throw new RuntimeException($response->json('error.message', 'Error al comunicarse con Wompi.'));
        }

        return $response->json();
    }

    public function getTransaction(string $idOrReference): array
    {
        $response = Http::withToken(config('wompi.private_key'))
            ->acceptJson()
            ->get(rtrim(config('wompi.api_url'), '/').'/v1/transactions/'.$idOrReference);

        if ($response->failed()) {
            throw new RuntimeException('No fue posible obtener la transacción de Wompi.');
        }

        return $response->json();
    }

    public function verifySignature(string $payload, string $headerSignature): bool
    {
        if (blank($headerSignature) || blank(config('wompi.events_secret'))) {
            return false;
        }

        $parts = collect(explode(',', $headerSignature))
            ->mapWithKeys(function ($part) {
                [$key, $value] = array_map('trim', explode('=', $part));

                return [$key => $value];
            });

        $signature = $parts->get('signature') ?? $parts->get('event_signature');
        $timestamp = $parts->get('timestamp');

        if (!$signature || !$timestamp) {
            return false;
        }

        $computed = hash_hmac('sha256', $timestamp.'.'.$payload, config('wompi.events_secret'));

        return hash_equals($signature, $computed);
    }
}
