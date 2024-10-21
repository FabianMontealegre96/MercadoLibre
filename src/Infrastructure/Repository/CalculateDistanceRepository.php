<?php

namespace Infrastructure\Repository;

use Domain\Interfaces\CalculateDistanceRepositoryInterface;
use PDO;
use PDOException;

class CalculateDistanceRepository implements CalculateDistanceRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $dbConnection)
    {
        $this->pdo = $dbConnection;
    }

    public function getNearestAndFurthest(): array
    {
        $query = "SELECT 
                    MAX_DISTANCE.country_name AS 'furthest_country', 
                    MAX_DISTANCE.distance AS 'furthest_distance',
                    MAX_DISTANCE.request_count AS 'furthest_request',
                    MIN_DISTANCE.country_name AS 'nearest_country', 
                    MIN_DISTANCE.distance AS 'nearest_distance',
                    MIN_DISTANCE.request_count AS 'nearest_request'
                FROM 
                    (SELECT country_name, distance, request_count 
                     FROM country_ips 
                     ORDER BY distance DESC 
                     LIMIT 1) AS MAX_DISTANCE, 
                    (SELECT country_name, distance, request_count 
                     FROM country_ips 
                     ORDER BY distance ASC 
                     LIMIT 1) AS MIN_DISTANCE";
        try {
            $stmt = $this->pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
