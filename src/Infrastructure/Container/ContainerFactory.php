<?php

namespace Infrastructure\Container;

use DI\ContainerBuilder;
use Domain\Entities\DBAccess;
use Domain\UseCases\GetCalculateDistanceUseCase;
use Domain\UseCases\GetExchangeRateFromIPUseCase;
use Exception;
use Infrastructure\Controllers\CalculateDistanceController;
use Infrastructure\Controllers\IpLocalizationController;
use Infrastructure\CurlRequesValidate;
use Infrastructure\Repository\CalculateDistanceRepository;
use Infrastructure\Repository\CountryIpRepository;
use Infrastructure\Services\ExchangeRateService;
use Infrastructure\Services\IPGeolocationService;
use PDO;

class ContainerFactory
{
    /**
     * @throws Exception
     */
    public static function create(): \Psr\Container\ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            // Configuración de la base de datos utilizando DBAccess
            DBAccess::class => function () {
                return new DBAccess();
            },

            PDO::class => function (\Psr\Container\ContainerInterface $c) {
                return $c->get(DBAccess::class)->getDBConnection();
            },

            // Repositorio
            CountryIpRepository::class => function (\Psr\Container\ContainerInterface $c) {
                return new CountryIpRepository($c->get(PDO::class));
            },
            CalculateDistanceRepository::class => function (\Psr\Container\ContainerInterface $c) {
                return new CalculateDistanceRepository($c->get(PDO::class));
            },

            // Servicio cURL para hacer peticiones
            CurlRequesValidate::class => function () {
                return new CurlRequesValidate();
            },

            // Servicio de geolocalización (IPGeolocationService) con inyección de CurlRequesValidate y API_KEY
            IPGeolocationService::class => function (\Psr\Container\ContainerInterface $c) {
                $apiKey = getenv('IP_GEOLOCATION_API_KEY');
                return new IPGeolocationService(
                    $c->get(CurlRequesValidate::class),
                    $apiKey
                );
            },
            ExchangeRateService::class => function (\Psr\Container\ContainerInterface $c) {
                $apiKey = getenv('EXCHANGE_RATE_API_KEY');
                return new ExchangeRateService(
                    $c->get(CurlRequesValidate::class),
                    $apiKey
                );
            },

            // Caso de uso GetExchangeRateFromIPUseCase
            GetExchangeRateFromIPUseCase::class => function (\Psr\Container\ContainerInterface $c) {
                return new GetExchangeRateFromIPUseCase(
                    $c->get(IPGeolocationService::class),
                    $c->get(CountryIpRepository::class),
                    $c->get(ExchangeRateService::class)
                );
            },
            GetCalculateDistanceUseCase::class => function (\Psr\Container\ContainerInterface $c) {
                return new GetCalculateDistanceUseCase(
                    $c->get(CalculateDistanceRepository::class)
                );
            },

            // Controlador
            IpLocalizationController::class => function (\Psr\Container\ContainerInterface $c) {
                return new IpLocalizationController(
                    $c->get(GetExchangeRateFromIPUseCase::class)
                );
            },

            CalculateDistanceController::class => function (\Psr\Container\ContainerInterface $c) {
                return new CalculateDistanceController(
                    $c->get(GetCalculateDistanceUseCase::class)
                );
            }
        ]);

        return $containerBuilder->build();
    }
}
