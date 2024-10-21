<?php

namespace Infrastructure\Controllers;

use Domain\Interfaces\GetExchangeRateFromIPUseCaseInterface;
use Exception;

class IpLocalizationController
{
    private GetExchangeRateFromIPUseCaseInterface $useCase;

    public function __construct(GetExchangeRateFromIPUseCaseInterface $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(string $ip): array
    {
        try {
            return $this->useCase->execute($ip);
        } catch (Exception $exception) {
            return [
                "fail" => 'Failed to get IP information',
                "error" => $exception->getMessage()
            ];
        }
    }
}
