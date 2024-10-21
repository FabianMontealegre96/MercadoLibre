# Proyecto Geolocalización de IP (Prueba mercado libre)

Este proyecto permite consultar información sobre la ubicación de una IP,
incluyendo datos del país, moneda, tasa de cambio y la
distancia de la ubicación a Buenos Aires. Utiliza una API de geolocalización y una
API de tasa de cambio, almacenando la información en una base de datos para futuras consultas.

## Características

- Geolocalización de una IP.
- Cálculo de la distancia entre la ubicación geolocalizada y Buenos Aires.
- Consulta de tasas de cambio según la moneda del país.
- Almacenamiento de datos de consulta en la base de datos (mysql para este proyecto).
- API REST para consultar la información desde Postman o cualquier cliente HTTP.

## Requisitos previos

- PHP >= 8.2
- Composer >= 2.8.1
- Docker
- Docker compose
- MySQL (opcional para ambiente local sin docker)
- XAMPP (opcional para ambiente local)
- PHPUnit >= 11 para pruebas unitarias
- mod_rewrite activo en apache para un correcto funcionamiento de htaccess.

## Instalación con docker

1. Clona el repositorio:

```bash
https://github.com/FabianMontealegre96/MercadoLibre.git
```

2. Navega al directorio del proyecto desde consola:

```bash
cd tu_proyecto
```

3. Modifica el archivo docker-compose.yml y reemplaza los siguientes valores a tu conveniencia

```yml
environment:
  MYSQL_ROOT_PASSWORD: tu_password
  MYSQL_USER: tu_usuario
  MYSQL_PASSWORD: tu_userpassword
```

4. Crea un archivo .env en la raíz del proyecto con la configuración de la base de
   datos y las API keys necesarias. Considerando que DB_USER debe ser igual a MYSQL_USER
   y DB_PASSWORD igual a MYSQL_PASSWORD asignados en el paso anterior:

```bash
DB_HOST=mysql-db
DB_NAME=ipSearchLocation
DB_USER=tu_usuario
DB_PASSWORD=tu_userpassword

API_KEY_GEOLOCATION=tu_api_key_geolocation
API_KEY_EXCHANGERATE=tu_api_key_exchange_rate
```

5. Ejecutar el siguiente comando

```bash
docker-compose up --build
```

6. Una vez realizada la configuracion podemos ir a la seccion de [Uso](#uso)

## Instalación sin docker

1. Clona el repositorio:

```bash
https://github.com/FabianMontealegre96/MercadoLibre.git
```

2. Navega al directorio del proyecto desde consola:

```bash
cd tu_proyecto
```

3. Instala las dependencias del proyecto usando Composer:

```bash
composer install
```

4. Crea un archivo .env en la raíz del proyecto con la configuración de la base de
   datos y las API keys necesarias:

```bash
DB_HOST=localhost
DB_NAME=ipSearchLocation
DB_USER=root
DB_PASSWORD=

API_KEY_GEOLOCATION=tu_api_key_geolocation
API_KEY_EXCHANGERATE=tu_api_key_exchange_rate
```

5. Crea un archivo .htaccess en la raíz del proyecto con la configuración
   de enrutamiento url:

```bash
RewriteEngine On

# Reescribe la URL 'SearchIP/1.1.1.1' (o cualquier IP) a 'GetDataIp.php?ip=1.1.1.1'
RewriteRule ^SearchIP/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$ public/GetDataIp.php?ip=$1 [L]
# Reescribe la URL 'StatisticsDistance a GetStatistics.php
RewriteRule ^StatisticsDistance$ public/GetStatistics.php [L]

```

6. Ejecuta el script SQL de creación de la BD ipSearchLocation y la tabla country_ips
   en tu base de datos MySQL (asegúrate de haber configurado correctamente la conexión):
   El archivo de creacion SQL se encuentra en la carpeta ./db-ini/init.sql

## Uso

### Ejecución local.

Para ejecutar el proyecto localmente puedes usar XAMPP o cualquier otro servidor web compatible con PHP.
Asegurarse de la existencia del archivo .htaccess

### API Endpoints

1. Consultar geolocalización y tasa de cambio de la IP consultada

- URL: /SearchIP/{ip}
- Método: GET
- Ejemplo de invocacion (postman):

```bash
GET http://ruta_proyecto/{miProyecto}/SearchIP/103.110.160.1
```

- Respuesta:

```json
{
  "country_name": "France",
  "country_code": "FR",
  "languages": "fr-FR,frp,br,co,ca,eu,oc",
  "currency_code": "EUR",
  "date_time": "2024-10-21 01:38:17.758+0200",
  "distance_to_BA": "13142.246 km",
  "exchange_rate": "1.0855"
}
```

2. Consulta de la distancias

- URL: /StatisticsDistance
- Método: GET
- Ejemplo de invocacion (postman):

```bash
GET http://ruta_proyecto/{miProyecto}/StatisticsDistance
```

- Respuesta:

```json
{
  "furthest_country": {
    "country": "France",
    "distance": "13142.246 km",
    "request": 8
  },
  "nearest_country": {
    "country": "Colombia",
    "distance": "7153.925 km",
    "request": 24
  },
  "invocation_average": {
    "average": 8651.005
  }
}
```

## Pruebas unitarias

El proyecto incluye pruebas unitarias usando PHPUnit.

1. Crear fichero phpunit.xml en la raiz del proyecto.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php"
         colors="true"
         stopOnFailure="false">
    <testsuites>
        <testsuite name="Project Test Suite">
            <directory>./tests</directory>
        </testsuite>
    </testsuites>
</phpunit>

```

1. Crear alias para ejecucion phpunit.bat (opcional)

```bash
Set-Alias phpunit 'ruta_proyecto\GeoIpInfo\vendor\bin\phpunit.bat'  
```

2. Ejecutar las pruebas unitarias

```bash
phpunit ruta_proyecto\GeoIpInfo\tests\Unit  
```

### Anotación

El comando Set-Alias es temporal. Si el proyecto es cerrado del editor
o si fue ejecutado desde consola y esta es cerrada, se debera
ejecutar nuevamente el comando.
  