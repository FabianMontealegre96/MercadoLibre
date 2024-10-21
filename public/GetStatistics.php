<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Config/envLoader.php';

use Infrastructure\Container\ContainerFactory;
use Infrastructure\Controllers\CalculateDistanceController;

try {
    $container = ContainerFactory::create();
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

try {
    $controller = $container->get(CalculateDistanceController::class);
    $response = $controller->handle();
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}


