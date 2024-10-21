<?php

/**
 * Carga las variables de entorno desde el archivo .env.
 *
 * @param string $envFile Ruta al archivo .env
 */
function loadEnv(string $envFile): void
{
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Ignorar comentarios
            if (str_starts_with($line, '#')) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    } else {
        die('.env file not found');
    }
}

// Cargar el archivo .env
loadEnv(__DIR__ . '/../../.env');
