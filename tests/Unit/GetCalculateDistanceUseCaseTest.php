<?php

use Domain\Interfaces\CalculateDistanceRepositoryInterface;
use Domain\UseCases\GetCalculateDistanceUseCase;
use Helpers\DistanceCalculator;
use PHPUnit\Framework\TestCase;

class GetCalculateDistanceUseCaseTest extends TestCase
{
    private $useCase;

    public function testValidateCorrectStructure()
    {
        $result = $this->useCase->execute();

        // Validar que la estructura del resultado sea la esperada
        $this->assertIsArray($result);
        $this->assertArrayHasKey('furthest_country', $result, "La clave furthest_country no existe");
        $this->assertArrayHasKey('nearest_country', $result, "La clave nearest_country no existe");
        $this->assertArrayHasKey('invocation_average', $result, "La clave invocation_average no existe");
    }

    public function testValidateStructureFurthestCountry()
    {
        $result = $this->useCase->execute();

        // Comprobar el contenido de la distancia más lejana
        $this->assertEquals('France', $result['furthest_country']['country'], "El valor de la clave furthest_country->country no es el esperado");
        $this->assertEquals('13142.246 km', $result['furthest_country']['distance'], "El valor de la clave furthest_country->distance no es el esperado");
        $this->assertEquals(8, $result['furthest_country']['request'], "El valor de la clave furthest_country->request no es el esperado");
    }

    public function testValidateStructureNearestCountry()
    {
        $result = $this->useCase->execute();

        // Comprobar el contenido de la distancia más cercana
        $this->assertEquals('Colombia', $result['nearest_country']['country'], "El valor de la clave nearest_country->country no es el esperado");
        $this->assertEquals('7153.925 km', $result['nearest_country']['distance'], "El valor de la clave nearest_country->distance no es el esperado");
        $this->assertEquals(23, $result['nearest_country']['request'], "El valor de la clave nearest_country->request no es el esperado");
    }

    public function testValidateStructureAverageCalculation()
    {
        $result = $this->useCase->execute();

        // Validar que se haya calculado correctamente el promedio de invocaciones
        $expectedAverage = DistanceCalculator::averageCalculate([
            'furthest_distance' => 13142.246,
            'furthest_request' => 8,
            'nearest_distance' => 7153.925,
            'nearest_request' => 23
        ]);
        $this->assertEquals($expectedAverage, $result['invocation_average']['average']);
    }

    protected function setUp(): void
    {
        // Crear un mock del repositorio que implementa la interfaz
        $mockRepo = $this->createMock(CalculateDistanceRepositoryInterface::class);

        // Definir el comportamiento del método getNearestAndFurthest para el mock
        $mockRepo->method('getNearestAndFurthest')
            ->willReturn([
                [
                    'furthest_country' => 'France',
                    'furthest_distance' => 13142.246,
                    'furthest_request' => 8,
                    'nearest_country' => 'Colombia',
                    'nearest_distance' => 7153.925,
                    'nearest_request' => 23
                ]
            ]);

        // Instanciar el caso de uso con el mock del repositorio
        $this->useCase = new GetCalculateDistanceUseCase($mockRepo);
    }
}
