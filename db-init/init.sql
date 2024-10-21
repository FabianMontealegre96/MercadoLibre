CREATE
DATABASE IF NOT EXISTS ipSearchLocation;

USE
ipSearchLocation;

CREATE TABLE IF NOT EXISTS country_ips
(
    id
    INT
    AUTO_INCREMENT
    PRIMARY
    KEY,
    country_name
    VARCHAR
(
    255
) NOT NULL,
    country_code CHAR
(
    2
) NOT NULL,
    languages VARCHAR
(
    255
) NOT NULL,
    currency_code CHAR
(
    3
) NOT NULL,
    ip VARCHAR
(
    45
) NOT NULL,
    request_count INT DEFAULT 1,
    latitude VARCHAR
(
    12
),
    longitude VARCHAR
(
    12
),
    distance FLOAT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE
(
    ip,
    country_code
)
    );

-- Índices para mejorar el rendimiento de las consultas
CREATE INDEX idx_country_code ON country_ips (country_code);
CREATE INDEX idx_ip ON country_ips (ip);
