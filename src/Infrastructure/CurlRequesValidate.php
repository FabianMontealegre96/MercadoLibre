<?php

namespace Infrastructure;

use Exception;

class CurlRequesValidate
{
    private mixed $maxRetries;
    private mixed $timeout;

    public function __construct($maxRetries = 3, $timeout = 30)
    {
        $this->maxRetries = $maxRetries;
        $this->timeout = $timeout;
    }

    public function curlRequestWithRetries($url)
    {
        $attempts = 0;
        $success = false;
        $response = null;

        while ($attempts < $this->maxRetries && !$success) {
            $ch = curl_init();

            // Configuración cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Verifica si hubo un error en la solicitud
            if (curl_errno($ch)) {
                $this->handleCurlError(curl_error($ch));
            }

            curl_close($ch);

            // Si la respuesta es exitosa, salir del bucle
            if ($httpCode == 200) {
                $success = true;
            } else {
                $attempts++;
                $this->handleHttpError($httpCode, $attempts);
                sleep(1);
            }
        }

        if (!$success) {
            throw new Exception("No se pudo completar la solicitud después de {$this->maxRetries} intentos.");
        }

        return $response;
    }

    // Método para manejar errores de cURL
    private function handleCurlError($error)
    {
        // Aquí puedes registrar el error o lanzar una excepción
        throw new Exception("Error cURL: " . $error);
    }

    // Método para manejar errores HTTP
    private function handleHttpError($httpCode, $attempts)
    {
        // Aquí puedes registrar el error o lanzar una excepción
        echo "Intento $attempts fallido. Código HTTP: $httpCode\n";
    }
}
