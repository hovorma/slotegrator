<?php

declare(strict_types=1);

namespace App\Parser;

use Symfony\Component\DomCrawler\Crawler;

class WebsiteAlzaCzProductParser implements ProductParserInterface
{
    public function supports(string $url): bool
    {
        return str_contains($url, 'alza.cz');
    }

    public function parse(string $html): array
    {
        $crawler = new Crawler($html);

        $name        = $crawler->filter('.h1-placeholder')->count() ? $crawler->filter('.h1-placeholder')->text() : null;
        $price       = $crawler->filter('.price-box__price')->count() ? $crawler->filter('.price-box__price')->text() : null;
        $image       = $crawler->filter('.swiper-slide-active img')->count() ? $crawler->filter('.swiper-slide-active img')->attr('src') : null;
        $description = $crawler->filter('.nameextc span')->count() ? $crawler->filter('.nameextc span')->text() : null;

        $price = $price ? $this->sanitizePrice($price) : null;

        return [
            'name'        => $name,
            'price'       => $price,
            'image'       => $image,
            'description' => $description,
        ];
    }

    private function sanitizePrice(string $price): float
    {
        $price = preg_replace('/[^\d,.-]/', '', $price);
        $price = str_replace(',', '.', $price);

        return (float)$price;
    }
}