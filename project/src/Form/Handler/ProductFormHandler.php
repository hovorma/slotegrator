<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\Product;
use App\Form\ProductType;
use App\Service\ProductService;
use LogicException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

class ProductFormHandler
{
    private ?FormInterface $form = null;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly ProductService       $productService
    )
    {
    }

    public function handle(Request $request, ?Product $product = null): bool
    {
        if (!$this->form) {
            $this->form = $this->formFactory->create(ProductType::class, $product);
        }

        $this->form->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $product = $this->form->getData();
            $this->productService->save($product);

            return true;
        }

        return false;
    }

    public function getFormView(): FormView
    {
        if (!$this->form) {
            throw new LogicException('The form must be created with handle() before rendering.');
        }

        return $this->form->createView();
    }
}