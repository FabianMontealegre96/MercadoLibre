<?php

namespace Infrastructure\Controllers;

use Domain\Interfaces\GetCalculateDistanceUseCaseInterface;
use Exception;

class CalculateDistanceController
{
    private GetCalculateDistanceUseCaseInterface $useCase;

    public function __construct(GetCalculateDistanceUseCaseInterface $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(): array
    {
        try {
            return $this->useCase->execute();
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
