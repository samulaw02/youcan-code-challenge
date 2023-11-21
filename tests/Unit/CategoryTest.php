<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CategoryService;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Cache\CacheManager;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Queue;


class CategoryTest extends TestCase
{

    protected CategoryService $categoryService;
    protected $categoryRepository;
    protected $cacheManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->categoryRepository = new CategoryRepository(new Category());
        $this->cacheManager = new CacheManager($this->createApplication());
        $this->categoryService = new CategoryService($this->categoryRepository, $this->cacheManager);
        Queue::fake();
    }

    public function test_get_all_categories_paginated()
    {
        $result = $this->categoryService->getAllCategoriesPaginated([]);
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertNotEmpty($result->items());
    }


    public function test_get_category_by_id()
    {
        $productData = [
            'name' => 'Test Category',
        ];

        $createdCategory = $this->categoryService->createCategory($productData);
        // Retrieve the product by its ID
        $retrievedProduct = $this->categoryService->getCategoryById($createdCategory->id);
        $this->assertInstanceOf(Category::class, $retrievedProduct) &&
        $this->assertEquals($createdCategory->id, $retrievedProduct->id);
    }

    public function test_create_category()
    {
        $categoryData = [
            'name' => 'New Category'
        ];
        $category = $this->categoryService->createCategory($categoryData);
        $this->assertInstanceOf(Category::class, $category);
    }

    public function test_update_category()
    {
        $randomCategory = Category::inRandomOrder()->first();
        $categoryData = [
            'name' => 'Updated Category Name'
        ];
        $category = $this->categoryService->getCategoryById($randomCategory->id);
        $updatedCategory = $this->categoryService->updateCategory($category, $categoryData);
        $this->assertInstanceOf(Category::class, $updatedCategory) &&
        $this->assertEquals('Updated Category Name', $updatedCategory->name);
    }

    public function test_delete_category()
    {
        $randomCategory = Category::inRandomOrder()->first();
        $result = $this->categoryService->deleteCategory($randomCategory->id);
        $this->assertTrue($result);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
