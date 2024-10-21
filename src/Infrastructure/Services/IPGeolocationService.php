<?php

namespace Infrastructure\Services;

use Application\Services\IPGeolocationServiceInterface;
use Exception;
use Infrastructure\CurlRequesValidate;

class IPGeolocationService implements IPGeolocationServiceInterface
{
    private CurlRequesValidate $curlRequestValidator;
    private string $apiKey;

    public function __construct(CurlRequesValidate $curlRequestValidator, string $apiKey)
    {
        $this->curlRequestValidator = $curlRequestValidator;
        $this->apiKey = $apiKey;
    }

    public function getCountryByIP(string $ip): array
    {
        $apiUrl = sprintf("https://api.ipgeolocation.io/ipgeo?apiKey=%s&ip=%s", $this->apiKey, $ip);

        try {
            $response = $this->curlRequestValidator->curlRequestWithRetries($apiUrl);

            $data = json_decode($response, true);

            if (!empty($data)) {
                return $data;
            } else {
                throw new Exception("Country not found in API response.");
            }
        } catch (Exception $e) {
            throw new Exception("Error fetching IP geolocation: " . $e->getMessage());
        }
    }
}
