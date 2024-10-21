<?php

namespace Domain\UseCases;

use Application\Services\ExchangeRateServiceInterface;
use Application\Services\IPGeolocationServiceInterface;
use Domain\Interfaces\CountryIpRepositoryInterface;
use Domain\Interfaces\GetExchangeRateFromIPUseCaseInterface;
use Exception;
use Helpers\DistanceCalculator;

class GetExchangeRateFromIPUseCase implements GetExchangeRateFromIPUseCaseInterface
{
    private IPGeolocationServiceInterface $ipGeolocationService;
    private CountryIpRepositoryInterface $countryIPRepository;
    private ExchangeRateServiceInterface $exchangeRateService;

    public function __construct(
        IPGeolocationServiceInterface $ipGeolocationService,
        CountryIpRepositoryInterface  $countryIPRepository,
        ExchangeRateServiceInterface  $exchangeRateService
    )
    {
        $this->ipGeolocationService = $ipGeolocationService;
        $this->countryIPRepository = $countryIPRepository;
        $this->exchangeRateService = $exchangeRateService;
    }

    public function execute($ip): array
    {
        try {
            // Obtener los datos de geolocalización
            $geoData = $this->getGeolocationData($ip);

            $countryName = $geoData['country_name'];
            $countryCode = $geoData['country_code2'];
            $languages = $geoData['languages'];
            $currencyCode = $geoData['currency']['code'];
            $normalDate = $geoData['time_zone']['current_time'];
            $latitude = $geoData['latitude'];
            $longitude = $geoData['longitude'];

            $exchagaRate = $this->exchangeRateService->handle($currencyCode);

            // Calcular distancia a Buenos Aires
            $distance = DistanceCalculator::calculateDistance($latitude, $longitude);

            // Formatear la información del país
            $arrDataCountry = [
                "countryName" => $countryName,
                "countryCode" => $countryCode,
                "languages" => $languages,
                "currencyCode" => $currencyCode,
                "ip" => $ip,
                "latitude" => $latitude,
                "longitude" => $longitude,
                "distance" => $distance
            ];

            // Guardar los datos en la base de datos
            $this->countryIPRepository->saveCountryIpData($arrDataCountry);

            // Retornar la respuesta con la información
            return [
                'country_name' => $countryName,
                'country_code' => $countryCode,
                'languages' => $languages,
                'currency_code' => $currencyCode,
                'date_time' => $normalDate,
                'distance_to_BA' => round($distance, 3) . " km",
                'exchange_rate' => $exchagaRate
            ];
        } catch (Exception $e) {
            throw $e;
        }

    }

    private function getGeolocationData($ip): array
    {
        try {
            return $this->ipGeolocationService->getCountryByIP($ip);
        } catch (Exception $e) {
            throw new Exception("Error to get geolocalización IP: " . $e->getMessage());
        }
    }
}
