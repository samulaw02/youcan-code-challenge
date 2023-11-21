<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\ProductCategoryService;


class ProductCategorySeeder extends Seeder
{
    /**
     * @var ProductCategoryService
     */
    private $productCategoryService;

    /**
     * ProductCategoryService constructor.
     *
     * @param ProductCategoryService $productCategoryService
     */
    public function __construct(ProductCategoryService $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->productCategoryService->attachProductsToCategories();
    }
}
