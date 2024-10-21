<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Config/envLoader.php';

use Infrastructure\Container\ContainerFactory;
use Infrastructure\Controllers\IpLocalizationController;

try {
    $container = ContainerFactory::create();
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

if (isset($_GET['ip']) && filter_var($_GET['ip'], FILTER_VALIDATE_IP)) {
    $ip = $_GET['ip'];
    try {
        $controller = $container->get(IpLocalizationController::class);
        $locationData = $controller->handle($ip);
        header('Content-Type: application/json');
        echo json_encode($locationData);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }


} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'IP inválida o no proporcionada']);
}

