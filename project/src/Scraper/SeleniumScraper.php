<?php

declare(strict_types=1);

namespace App\Scraper;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class SeleniumScraper implements ScraperInterface
{
    private string $seleniumUrl;

    public function __construct(string $seleniumUrl)
    {
        $this->seleniumUrl = $seleniumUrl;
    }

    public function fetchPageContent(string $url): string
    {
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($this->seleniumUrl, $capabilities);

        try {
            $driver->get($url);

            return $driver->getPageSource();
        } finally {
            $driver->quit();
        }
    }
}