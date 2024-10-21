<?php

namespace Infrastructure\Services;

use Application\Services\ExchangeRateServiceInterface;
use Exception;
use Infrastructure\CurlRequesValidate;

class ExchangeRateService implements ExchangeRateServiceInterface
{
    private CurlRequesValidate $curlRequestValidator;
    private string $apiKey;

    public function __construct(CurlRequesValidate $curlRequestValidator, string $apiKey)
    {
        $this->curlRequestValidator = $curlRequestValidator;
        $this->apiKey = $apiKey;
    }

    public function handle(string $currencyCode): string
    {
        // Obtener la tasa de cambio
        $apiUrl = sprintf("https://v6.exchangerate-api.com/v6/%s/pair/%s/USD", $this->apiKey, $currencyCode);

        try {
            $response = $this->curlRequestValidator->curlRequestWithRetries($apiUrl);
            $data = json_decode($response, true);
            if (!empty($data)) {
                return $data['conversion_rate'];
            } else {
                throw new Exception("Country not found in API response.");
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
