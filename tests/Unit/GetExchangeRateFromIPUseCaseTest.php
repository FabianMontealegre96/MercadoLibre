<?php

use Application\Services\ExchangeRateServiceInterface;
use Application\Services\IPGeolocationServiceInterface;
use Domain\Interfaces\CountryIpRepositoryInterface;
use Domain\UseCases\GetExchangeRateFromIPUseCase;
use Helpers\DistanceCalculator;
use PHPUnit\Framework\TestCase;

class GetExchangeRateFromIPUseCaseTest extends TestCase
{
    private $mockGeolocationService;
    private $mockCountryIPRepository;
    private $mockExchangeRateService;
    private $useCase;

    public function testValidateCorrectStructureIpInfo()
    {
        // Definir el comportamiento de los mocks
        $geoData = [
            'country_name' => 'Colombia',
            'country_code2' => 'CO',
            'languages' => 'es-CO',
            'currency' => ['code' => 'COP'],
            'time_zone' => ['current_time' => '2024-10-17T10:00:00'],
            'latitude' => -34.603722,
            'longitude' => -58.381592
        ];

        $this->mockGeolocationService->method('getCountryByIP')->willReturn($geoData);
        $this->mockExchangeRateService->method('handle')->willReturn("3800.50");

        $result = $this->useCase->execute('181.53.14.19');

        // Verificar la estructura de la respuesta
        $this->assertIsArray($result, "Se espera que el resultado sea de tipo Array");
        $this->assertArrayHasKey('country_name', $result, "La clave country_name no existe");
        $this->assertArrayHasKey('country_code', $result, "La clave country_code no existe");
        $this->assertArrayHasKey('languages', $result, "La clave languages no existe");
        $this->assertArrayHasKey('currency_code', $result, "La clave currency no existe");
        $this->assertArrayHasKey('date_time', $result, "La clave date_time no existe");
        $this->assertArrayHasKey('distance_to_BA', $result, "La clave distance to_BA no existe");
        $this->assertArrayHasKey('exchange_rate', $result, "La clave exchange rate no existe");
    }

    public function testValidateStructureGeolocationData()
    {
        $geoData = [
            'country_name' => 'Colombia',
            'country_code2' => 'CO',
            'languages' => 'es-CO',
            'currency' => ['code' => 'COP'],
            'time_zone' => ['current_time' => '2024-10-17T10:00:00'],
            'latitude' => -34.603722,
            'longitude' => -58.381592
        ];

        // Configurar la respuesta del mock
        $this->mockGeolocationService->method('getCountryByIP')->willReturn($geoData);

        $result = $this->useCase->execute('181.53.14.19');

        // Comprobar que los datos de geolocalización están correctamente procesados
        $this->assertEquals('Colombia', $result['country_name'], "El resultado de country_name no es igual a lo esperado");
        $this->assertEquals('CO', $result['country_code'], "El resultado de country_code no es igual a lo esperado");
        $this->assertEquals('es-CO', $result['languages'], "El resultado de languages no es igual a lo esperado");
        $this->assertEquals('COP', $result['currency_code'], "El resultado de currency_code no es igual a lo esperado");
        $this->assertEquals('2024-10-17T10:00:00', $result['date_time'], "El resultado de date_time no es igual a lo esperado");
    }

    public function testValidateDistanceCalculation()
    {
        $geoData = [
            'country_name' => 'Colombia',
            'country_code2' => 'CO',
            'languages' => 'es-CO',
            'currency' => ['code' => 'COP'],
            'time_zone' => ['current_time' => '2024-10-17T10:00:00'],
            'latitude' => -34.603722,
            'longitude' => -58.381592
        ];

        // Configurar la respuesta del mock de geolocalización
        $this->mockGeolocationService->method('getCountryByIP')->willReturn($geoData);

        // Probar si la distancia se calcula correctamente
        $result = $this->useCase->execute('181.53.14.19');
        $expectedDistance = DistanceCalculator::calculateDistance(-34.603722, -58.381592);

        // Validar la distancia a Buenos Aires
        $this->assertEquals(round($expectedDistance, 3) . " km", $result['distance_to_BA'], "El resultado de distance_BA no es igual a lo esperado");
    }

    public function testValidateCallExchangeRateService()
    {
        $geoData = [
            'country_name' => 'Colombia',
            'country_code2' => 'CO',
            'languages' => 'es-CO',
            'currency' => ['code' => 'COP'],
            'time_zone' => ['current_time' => '2024-10-17T10:00:00'],
            'latitude' => -34.603722,
            'longitude' => -58.381592
        ];

        // Configurar la respuesta del mock
        $this->mockGeolocationService->method('getCountryByIP')->willReturn($geoData);

        // Configurar el servicio de tasa de cambio para devolver un valor
        $this->mockExchangeRateService->method('handle')
            ->with('COP')
            ->willReturn("3800.50");

        $result = $this->useCase->execute('181.53.14.19');

        // Validar que se haya devuelto la tasa de cambio correcta
        $this->assertEquals(3800.50, $result['exchange_rate'], "El resultado de exchange_rate no es igual a lo esperado");
    }

    public function testValidateRepositorySave()
    {
        $geoData = [
            'country_name' => 'Colombia',
            'country_code2' => 'CO',
            'languages' => 'es-CO',
            'currency' => ['code' => 'COP'],
            'time_zone' => ['current_time' => '2024-10-17T10:00:00'],
            'latitude' => -34.603722,
            'longitude' => -58.381592
        ];

        // Configurar la respuesta del mock de geolocalización
        $this->mockGeolocationService->method('getCountryByIP')->willReturn($geoData);

        // Esperar que se llame al método saveCountryIpData
        $this->mockCountryIPRepository->expects($this->once())
            ->method('saveCountryIpData')
            ->with($this->arrayHasKey('countryName'));

        // Ejecutar el caso de uso
        $this->useCase->execute('181.53.14.19');
    }

    protected function setUp(): void
    {
        // Crear los mocks
        $this->mockGeolocationService = $this->createMock(IPGeolocationServiceInterface::class);
        $this->mockCountryIPRepository = $this->createMock(CountryIpRepositoryInterface::class);
        $this->mockExchangeRateService = $this->createMock(ExchangeRateServiceInterface::class);

        // Instanciar el caso de uso con los mocks
        $this->useCase = new GetExchangeRateFromIPUseCase(
            $this->mockGeolocationService,
            $this->mockCountryIPRepository,
            $this->mockExchangeRateService
        );
    }
}
