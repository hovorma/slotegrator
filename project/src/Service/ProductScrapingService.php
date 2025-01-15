<?php

declare(strict_types=1);

namespace App\Service;

use App\Factory\ProductScrapingFactoryInterface;

readonly class ProductScrapingService
{
    public function __construct(private ProductScrapingFactoryInterface $scrapingFactory)
    {
    }

    public function scrapeAndParseProduct(string $url): array
    {
        [$scraper, $parser] = $this->scrapingFactory->create($url);

        $html = $scraper->fetchPageContent($url);

        return $parser->parse($html);
    }
}