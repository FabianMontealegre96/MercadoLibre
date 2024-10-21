<?php

namespace Domain\UseCases;

use Domain\Interfaces\CalculateDistanceRepositoryInterface;
use Domain\Interfaces\GetCalculateDistanceUseCaseInterface;
use Exception;
use Helpers\DistanceCalculator;

class GetCalculateDistanceUseCase implements GetCalculateDistanceUseCaseInterface
{
    private CalculateDistanceRepositoryInterface $calculateDistance;

    public function __construct(
        CalculateDistanceRepositoryInterface $calculateDistance
    )
    {
        $this->calculateDistance = $calculateDistance;
    }

    public function execute(): array
    {
        try {
            $distanceData = [];
            $list = $this->calculateDistance->getNearestAndFurthest();

            foreach ($list as $key) {
                $arrDataAverage = [
                    "furthest_distance" => $key['furthest_distance'],
                    "furthest_request" => $key['furthest_request'],
                    "nearest_distance" => $key['nearest_distance'],
                    "nearest_request" => $key['nearest_request']
                ];

                $distanceData['furthest_country'] = [
                    "country" => $key['furthest_country'],
                    "distance" => $key['furthest_distance'] . " km",
                    "request" => $key['furthest_request']
                ];

                $distanceData['nearest_country'] = [
                    "country" => $key['nearest_country'],
                    "distance" => $key['nearest_distance'] . " km",
                    "request" => $key['nearest_request']
                ];

                $distanceData['invocation_average'] = [
                    "average" => DistanceCalculator::averageCalculate($arrDataAverage)
                ];
            }
            return $distanceData;
        } catch (Exception $exception) {
            throw  $exception;
        }
    }
}
