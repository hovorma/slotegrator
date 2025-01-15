<?php

declare(strict_types=1);

namespace App\Factory;

interface ProductScrapingFactoryInterface
{
    public function create(string $url): array;
}