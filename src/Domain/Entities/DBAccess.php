<?php

namespace Domain\Entities;

use PDO;

class DBAccess
{
    public function getDBConnection(): PDO
    {
        return new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME'),
            getenv('DB_USER'),
            getenv('DB_PASSWORD')
        );
    }

}