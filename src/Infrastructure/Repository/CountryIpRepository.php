<?php

namespace Infrastructure\Repository;

use Domain\Interfaces\CountryIpRepositoryInterface;
use mysql_xdevapi\Exception;
use PDO;
use PDOException;

class CountryIpRepository implements CountryIpRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $dbConnection)
    {
        $this->pdo = $dbConnection;
    }

    public function saveCountryIpData(array $data): void
    {

        try {
            // Verificamos si la IP ya existe en la base de datos
            $stmt = $this->pdo->prepare("SELECT * FROM country_ips WHERE ip = :ip AND country_code = :country_code");
            $stmt->execute([
                'ip' => $data['ip'],
                'country_code' => $data['countryCode']
            ]);

            if ($stmt->rowCount() > 0) {
                // Si ya existe, realizamos un UPDATE
                $stmt = $this->pdo->prepare(
                    "UPDATE country_ips 
                SET request_count = request_count + 1, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE ip = :ip AND country_code = :country_code"
                );
                $stmt->execute([
                    'ip' => $data['ip'],
                    'country_code' => $data['countryCode']
                ]);
            } else {
                // Si no existe, realizamos un INSERT
                $stmt = $this->pdo->prepare(
                    "INSERT INTO country_ips 
                (country_name, country_code, languages, currency_code, ip, request_count, latitude, longitude, distance) 
                VALUES 
                (:country_name, :country_code, :languages, :currency_code, :ip, 1, :latitude, :longitude, :distance)"
                );
                $stmt->execute([
                    'country_name' => $data['countryName'],
                    'country_code' => $data['countryCode'],
                    'languages' => $data['languages'],
                    'currency_code' => $data['currencyCode'],
                    'ip' => $data['ip'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'distance' => $data['distance']
                ]);
            }
        } catch (PDOException|Exception $e) {
            throw $e;
        }
    }
}
