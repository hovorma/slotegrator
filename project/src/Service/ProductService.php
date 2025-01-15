<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;

readonly class ProductService
{
    public function __construct(
        private ProductRepository $repository,
    ) {}

    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    public function save(Product $product): void
    {
        $this->repository->save($product);
    }

    public function delete(Product $product): void
    {
        $this->repository->delete($product);
    }
}
