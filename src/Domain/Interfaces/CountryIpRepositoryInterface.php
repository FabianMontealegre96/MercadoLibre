<?php

namespace Domain\Interfaces;

interface CountryIpRepositoryInterface
{
    public function saveCountryIpData(array $data): void;
}
