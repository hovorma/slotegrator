<?php

declare(strict_types=1);

namespace App\Parser;

interface ProductParserInterface
{
    public function supports(string $url): bool;

    public function parse(string $html): array;
}