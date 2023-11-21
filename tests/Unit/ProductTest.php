<?php

namespace Tests\Unit;

use App\Models\Category;
use Tests\TestCase;
use App\Services\ProductService;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Cache\CacheManager;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Queue;


class ProductTest extends TestCase
{

    protected ProductService $productService;
    protected $productRepository;
    protected $cacheManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->productRepository = new ProductRepository(new Product());
        $this->cacheManager = new CacheManager($this->createApplication());
        $this->productService = new ProductService($this->productRepository, $this->cacheManager);
        Queue::fake();
    }

    public function test_get_all_products_paginated()
    {
        $result = $this->productService->getAllProductsPaginated();
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertNotEmpty($result->items());

    }


    public function test_get_product_by_id()
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'Description',
            'price' => 50.00,
            'image' => 'image.jpg',
        ];
        $productData['category_ids'] = Category::take(rand(1, 5))->pluck('id')->toArray();
        $createdProduct = $this->productService->createProduct($productData);
        // Retrieve the product by its ID
        $retrievedProduct = $this->productService->getProductById($createdProduct->id);
        $this->assertInstanceOf(Product::class, $retrievedProduct) &&
        $this->assertEquals($createdProduct->id, $retrievedProduct->id);
    }

    public function test_create_product()
    {
        $productData = [
            'name' => 'New Product',
            'description' => 'Description',
            'price' => 50.00,
            'image' => 'image.jpg',
        ];
        $productData['category_ids'] = Category::take(rand(1, 5))->pluck('id')->toArray();
        $product = $this->productService->createProduct($productData);
        $this->assertInstanceOf(Product::class, $product);
    }

    public function test_update_product()
    {
        $randomProduct = Product::inRandomOrder()->first();
        $productData = [
            'name' => 'Updated Product Name'
        ];
        $product = $this->productService->getProductById($randomProduct->id);
        $updatedProduct = $this->productService->updateProduct($product, $productData);
        $this->assertInstanceOf(Product::class, $updatedProduct) &&
        $this->assertEquals('Updated Product Name', $updatedProduct->name);
    }

    public function test_delete_product()
    {
        $randomProduct = Product::inRandomOrder()->first();
        $result = $this->productService->deleteProduct($randomProduct->id);
        $this->assertTrue($result);
    }

    public function test_search_for_product_by_category_name()
    {
        $category = Category::first();
        $query = [
            'global_search' => $category->name,
        ];
        $result = $this->productService->searchProducts($query);
        $this->assertInstanceOf(LengthAwarePaginator::class, $result) &&
        $this->assertEqual($result->data[0]->categories[0]->name, $category->name);
    }



    public function tearDown(): void
    {
        parent::tearDown();
    }
}
