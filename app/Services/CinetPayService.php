<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CinetPayService
{
    private string $apiKey;
    private string $siteId;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.cinetpay.api_key');
        $this->siteId  = config('services.cinetpay.site_id');
        $this->baseUrl = config('services.cinetpay.mode') === 'sandbox'
            ? 'https://api-checkout.cinetpay.com/v2'
            : 'https://api-checkout.cinetpay.com/v2';
    }

    // ── Initier un paiement ───────────────────────────
    public function initiatePayment(
        float  $amount,
        string $orderId,
        string $description,
        string $returnUrl,
        string $notifyUrl,
        string $currency = 'XOF'
    ): array {
        $transactionId = 'SC-' . strtoupper(Str::random(10));

        $response = Http::post($this->baseUrl . '/payment', [
            'apikey'              => $this->apiKey,
            'site_id'             => $this->siteId,
            'transaction_id'      => $transactionId,
            'amount'              => (int) $amount,
            'currency'            => $currency,
            'description'         => $description,
            'return_url'          => $returnUrl,
            'notify_url'          => $notifyUrl,
            'channels'            => 'MOBILE_MONEY',
            'metadata'            => json_encode(['order_id' => $orderId]),
            'lang'                => 'FR',
        ]);

        if ($response->successful() && $response->json('code') === '201') {
            return [
                'success'        => true,
                'payment_url'    => $response->json('data.payment_url'),
                'transaction_id' => $transactionId,
            ];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Erreur de paiement',
        ];
    }

    // ── Vérifier le statut d'un paiement ─────────────
    public function verifyPayment(string $transactionId): array
    {
        $response = Http::post($this->baseUrl . '/payment/check', [
            'apikey'         => $this->apiKey,
            'site_id'        => $this->siteId,
            'transaction_id' => $transactionId,
        ]);

        if ($response->successful()) {
            $data   = $response->json('data');
            $status = $data['status'] ?? 'UNKNOWN';

            return [
                'success' => true,
                'paid'    => $status === 'ACCEPTED',
                'status'  => $status,
                'data'    => $data,
            ];
        }

        return ['success' => false, 'paid' => false];
    }
}