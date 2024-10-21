<?php

namespace Application\Services;

interface IPGeolocationServiceInterface
{
    public function getCountryByIP(string $ip): array;
}