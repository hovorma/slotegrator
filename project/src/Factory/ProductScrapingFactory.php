<?php

declare(strict_types=1);

namespace App\Factory;

use App\Scraper\ScraperInterface;
use RuntimeException;

class ProductScrapingFactory implements ProductScrapingFactoryInterface
{
    private iterable $parsers;
    private ScraperInterface $scraper;

    public function __construct(iterable $parsers, ScraperInterface $scraper)
    {
        $this->parsers = $parsers;
        $this->scraper = $scraper;
    }

    public function create(string $url): array
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($url)) {
                return [$this->scraper, $parser];
            }
        }

        throw new RuntimeException('No parser found for the given URL.');
    }
}