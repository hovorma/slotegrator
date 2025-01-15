<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ProductScrapingService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ScrapingController extends AbstractController
{
    public function __construct(private readonly ProductScrapingService $productScrapingService)
    {

    }

    #[Route('/selenium-scrape', name: 'selenium_scrape', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $url  = $data['url'] ?? null;

        if (!$url) {
            return new JsonResponse(['message' => 'URL is required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $productData = $this->productScrapingService->scrapeAndParseProduct($url);

            return new JsonResponse([
                'status' => 'success',
                'data'   => $productData,
            ]);

        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()]);
        }
    }
}
