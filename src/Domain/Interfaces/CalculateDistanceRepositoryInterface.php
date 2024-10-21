<?php

namespace Domain\Interfaces;

interface CalculateDistanceRepositoryInterface
{
    public function getNearestAndFurthest(): array;
}