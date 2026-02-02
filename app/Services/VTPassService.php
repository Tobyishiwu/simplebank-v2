<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VTPassService
{
    protected $baseUrl;
    protected $apiKey;
    protected $secretKey;

    public function __construct()
    {
        // Using Sandbox for testing; change to https://api-service.vtpass.com/api for live
        $this->baseUrl = "https://sandbox.vtpass.com/api";
        $this->apiKey = config('services.vtpass.api_key');
        $this->secretKey = config('services.vtpass.secret_key');
    }

    /**
     * Unified Purchase Method
     * Handles Airtime, Data (variation_code), and Electricity (EEDC/Ikeja)
     */
    public function purchase(array $data)
    {
        $payload = [
            'request_id' => $data['request_id'],
            'serviceID'  => $data['service_id'],
            'amount'     => $data['amount'],
            'phone'      => $data['phone'] ?? $data['billers_code'],
        ];

        // Required for Data Bundles (e.g., m1024)
        if (!empty($data['variation_code'])) {
            $payload['variation_code'] = $data['variation_code'];
        }

        // Required for Electricity (EEDC, Ikeja, etc.)
        if (str_contains($data['service_id'], 'electric')) {
            $payload['subscription_type'] = 'prepaid';
            $payload['billersCode'] = $data['billers_code'];
        }

        $response = Http::withHeaders([
            'api-key' => $this->apiKey,
            'secret-key' => $this->secretKey,
        ])->post($this->baseUrl . '/pay', $payload);

        return $response->json();
    }

    /**
     * Verify Merchant/Meter (Optional but recommended for Electricity)
     */
    public function verifyMerchant($serviceID, $billersCode)
    {
        $response = Http::withHeaders([
            'api-key' => $this->apiKey,
            'secret-key' => $this->secretKey,
        ])->post($this->baseUrl . '/merchant-verify', [
            'serviceID' => $serviceID,
            'billersCode' => $billersCode,
        ]);

        return $response->json();
    }
}
