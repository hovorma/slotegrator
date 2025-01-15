<?php

declare(strict_types=1);

namespace App\Scraper;

interface ScraperInterface
{
    public function fetchPageContent(string $url): string;
}