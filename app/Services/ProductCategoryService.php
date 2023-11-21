<?php

namespace App\Services;

use App\Repositories\ProductCategoryRepository;


class ProductCategoryService
{
    protected $productCategoryRepository;


    public function __construct(ProductCategoryRepository $productCategoryRepository)
    {
        $this->productCategoryRepository = $productCategoryRepository;
    }

    //attach multiple product to categories
    public function attachProductsToCategories()
    {
        return $this->productCategoryRepository->attachProductsToCategories();
    }
}
