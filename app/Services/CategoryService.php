<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Illuminate\Cache\CacheManager;
use App\Models\Category;


class CategoryService
{
    protected $categoryRepository;
    protected $cacheManager;
    protected $cacheDuration = 5;
    protected $commonCacheTag = 'categories';


    public function __construct(CategoryRepository $categoryRepository, CacheManager $cacheManager)
    {
        $this->categoryRepository = $categoryRepository;
        $this->cacheManager = $cacheManager;
    }

    //fetch all categories
    public function getAllCategories()
    {
        return $this->categoryRepository->getAllWithProducts();
    }

    //fetch all paginated categories
    public function getAllCategoriesPaginated(array $data)
    {
        $page = $data['page'] ?? 1;
        $perPage = 10;
        $cacheKey = "categories_page_{$page}";
        $cacheKey = $this->generateCacheKey($data, $page);
        return $this->cacheManager->tags([$this->commonCacheTag])->remember($cacheKey, now()->addMinutes($this->cacheDuration), function () use($perPage, $page, $data) {
            return $this->categoryRepository->paginate($perPage, $page, $data);
        });
    }

    //generate redis cache key
    public function generateCacheKey($data, $page)
    {
        $name = $data['name'] ?? '';
        return "categories_page_{$page}_{$name}";
    }


    //fetch category by id
    public function getCategoryById($id)
    {
        // Generate a unique cache key based on the category ID
        $cacheKey = "category_{$id}_details";
        return $this->cacheManager->tags([$this->commonCacheTag])->remember($cacheKey, now()->addMinutes($this->cacheDuration), function () use($id) {
            return $this->categoryRepository->find($id);
        });
    }

    //create a category
    public function createCategory(array $data)
    {
        $category = $this->categoryRepository->create($data);
        $this->cacheManager->tags([$this->commonCacheTag])->flush();
        return $category;
    }

    //update category
    public function updateCategory(Category $category, array $data)
    {
        $updatedCategory = $this->categoryRepository->update($category, $data);
        // Generate a unique cache key based on the category ID
        $cacheKey = "category_{$updatedCategory->id}_details";
        $this->cacheManager->tags([$this->commonCacheTag])->put($cacheKey, $updatedCategory, now()->addMinutes($this->cacheDuration));
        return $updatedCategory;
    }

    //delete a category
    public function deleteCategory($id)
    {
        $this->cacheManager->tags([$this->commonCacheTag])->flush();
        return $this->categoryRepository->delete($id);
    }

    //seed categories to db
    public function seedCategories(array $data)
    {
        return $this->categoryRepository->insertToAllCategories($data);
    }
}
