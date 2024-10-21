<?php

namespace Domain\Interfaces;

namespace Domain\Interfaces;

interface GetExchangeRateFromIPUseCaseInterface
{
    public function execute(string $ip): array;
}
