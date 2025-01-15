<?php

declare(strict_types=1);

namespace App\Tests\Parser;

use App\Factory\ProductScrapingFactory;
use App\Parser\WebsiteAlzaCzProductParser;
use App\Scraper\ScraperInterface;
use App\Scraper\SeleniumScraper;
use PHPUnit\Framework\TestCase;

class AlzaParserTest extends TestCase
{
    public function testParseSuccessfullyExtractsProductData(): void
    {
        $htmlContent = <<<HTML
        <html>
            <head><title>Product Page</title></head>
            <body>
                <div id="h1c">
                    <h1 class="h1-placeholder">Test product name</h1>
                </div>
                <div class="price-box__price">1 490,-</div>
                <div class="swiper-slide-active">
                    <img class="productImage" src="https://example.com/product.jpg" />
                </div>
                <div class="nameextc">
                    <span>Test product Description</span>
                </div>
            </body>
        </html>
        HTML;

        $parser = new WebsiteAlzaCzProductParser();
        $productData = $parser->parse($htmlContent);

        $this->assertArrayHasKey('name', $productData, 'Product name should be present in the parsed data.');
        $this->assertEquals('Test product name', $productData['name'], 'The product name should match the expected value.');

        $this->assertArrayHasKey('price', $productData, 'Product price should be present in the parsed data.');
        $this->assertEquals(1490, $productData['price'], 'The product price should match the expected value.');

        $this->assertArrayHasKey('image', $productData, 'Product image should be present in the parsed data.');
        $this->assertEquals('https://example.com/product.jpg', $productData['image'], 'The product image URL should match the expected value.');

        $this->assertArrayHasKey('description', $productData, 'Product description should be present in the parsed data.');
        $this->assertEquals('Test product Description', $productData['description'], 'The product description should match the expected value.');
    }

    public function testParseHandlesInvalidHtmlGracefully(): void
    {
        $htmlContent = <<<HTML
        <html>
            <head><title>Broken Page</title></head>
            <body>
                <!-- Missing name, price, image, description -->
            </body>
        </html>
        HTML;

        $parser = new WebsiteAlzaCzProductParser();

        $productData = $parser->parse($htmlContent);

        $this->assertNotEmpty($productData, 'Parser should return an empty array when the HTML content is invalid.');
        $this->assertNull($productData['name'], 'The product name should be null when the HTML content is invalid.');
        $this->assertNull($productData['price'], 'The product price should be null when the HTML content is invalid.');
        $this->assertNull($productData['image'], 'The product image URL should be null when the HTML content is invalid.');
        $this->assertNull($productData['description'], 'The product description should be null when the HTML content is invalid.');
    }

    public function testParseWithMockedScraper(): void
    {
        $mockScraper = $this->createMock(ScraperInterface::class);

        $mockScraper->method('fetchPageContent')
            ->with('https://mock-url/alza-product')
            ->willReturn('<html>
                            <div id="h1c">
                                <h1 class="h1-placeholder">Mocked Product</h1>
                            </div>
                            <div class="price-box__price">999 Kč</div>
                            <div class="swiper-slide-active">
                                <img class="productImage" src="https://example.com/product.jpg" />
                            </div>
                          </html>');

        $parser = new WebsiteAlzaCzProductParser();

        $mockFactory = $this->createMock(ProductScrapingFactory::class);
        $mockFactory->method('create')
            ->with('https://mock-url/alza-product') // Simulate factory behavior
            ->willReturn([$mockScraper, $parser]);

        [$scraper, $retrievedParser] = $mockFactory->create('https://mock-url/alza-product');

        $htmlContent = $scraper->fetchPageContent('https://mock-url/alza-product');
        $productData = $retrievedParser->parse($htmlContent);

        $this->assertEquals('Mocked Product', $productData['name'], 'The product name should match.');
        $this->assertEquals(999, $productData['price'], 'The product price should match the sanitized value.');
        $this->assertEquals('https://example.com/product.jpg', $productData['image'], 'The product image URL should match.');

        $this->assertArrayHasKey('description', $productData);
        $this->assertNull($productData['description'], 'The description should be null for a mocked product.');
    }

    public function testSupportsMethod(): void
    {
        $parser = new WebsiteAlzaCzProductParser();

        $this->assertTrue($parser->supports('https://www.alza.cz/product-page'), 'Parser should support valid Alza.cz URLs.');
        $this->assertTrue($parser->supports('https://alza.cz/something-else'), 'Parser should support valid Alza.cz URLs without www.');
        $this->assertFalse($parser->supports('https://www.not-alza.com/product-page'), 'Parser should not support non-Alza.cz URLs.');
    }

    public function testIntegrationWithLiveAlzaWebsite(): void
    {
        $scraper = new SeleniumScraper($_ENV['SELENIUM_URL'] ?? '');
        $parser = new WebsiteAlzaCzProductParser();
        $factory = new ProductScrapingFactory([$parser], $scraper);

        $url = 'https://www.alza.cz/apple-silikonovy-kryt-s-magsafe-na-iphone-16-pro-max-denimovy-d12541439.htm?setlang=cs-CZ';

        [$scraper, $retrievedParser] = $factory->create($url);

        $html = $scraper->fetchPageContent($url);
        $productData = $retrievedParser->parse($html);

        $this->assertArrayHasKey('name', $productData, 'Product name should be present.');
        $this->assertArrayHasKey('price', $productData, 'Product price should be present.');
        $this->assertArrayHasKey('image', $productData, 'Product image should be present.');
        $this->assertArrayHasKey('description', $productData, 'Product description should be present.');

        $this->assertNotEmpty($productData['name'], 'Product name should not be empty.');
        $this->assertNotEmpty($productData['price'], 'Product price should not be empty.');
        $this->assertNotEmpty($productData['image'], 'Product image should not be empty.');
        $this->assertNotEmpty($productData['description'], 'Product description should not be empty.');
    }
}