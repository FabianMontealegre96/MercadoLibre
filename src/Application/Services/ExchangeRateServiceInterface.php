<?php

namespace Application\Services;

interface ExchangeRateServiceInterface
{
    public function handle(string $currencyCode): string;
}