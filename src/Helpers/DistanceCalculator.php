<?php

namespace Helpers;

class DistanceCalculator
{
    /**
     * Calcula la distancia en kilómetros entre dos puntos geográficos usando la fórmula de Haversine.
     * Por defecto usa las coordenadas de buenos aires para hacer el calculo
     *
     * @param float $lat1 Latitud del primer punto.
     * @param float $long1 Longitud del primer punto.
     * @param float $lat2 Latitud del segundo punto.
     * @param float $long2 Longitud del segundo punto.
     * @return float Distancia en kilómetros entre los dos puntos.
     */
    public static function calculateDistance($lat1, $long1, $lat2 = -58.3816, $long2 = -58.3816)
    {
        //radio de la tierra en km
        $earthRadius = 6371;

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($long1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($long2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public static function averageCalculate($data)
    {
        $furthestRequest = $data['furthest_request'];
        $nearestRequest = $data['nearest_request'];
        $furthestDistance = $data['furthest_distance'];
        $nearestDistance = $data['nearest_distance'];

        $sumRequest = $furthestRequest + $nearestRequest;
        $resultAverage = ($furthestDistance * $furthestRequest + $nearestDistance * $nearestRequest) / $sumRequest;
        return round($resultAverage, 3);
    }
}
